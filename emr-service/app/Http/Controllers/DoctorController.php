<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\LabResult;
use Carbon\Carbon;

class DoctorController extends Controller
{
    /**
     * Show the doctor dashboard.
     */
    public function dashboard(): View
    {
        // Doctor-specific statistics
        $todayPatients = Consultation::whereDate('consultation_date', today())->distinct('patient_id')->count('patient_id');
        $pendingConsultations = Consultation::where('status', 'pending')->count();
        $completedToday = Consultation::whereDate('consultation_date', today())
            ->where('status', 'completed')->count();
        $pendingLabReviews = LabResult::where('status', 'pending')->count();
        
        // Recent consultations for the doctor
        $recentConsultations = Consultation::with('patient')
            ->latest('consultation_date')
            ->limit(5)
            ->get();
        
        // Patients waiting (pending consultations from today)
        $waitingPatients = Consultation::with('patient')
            ->whereDate('consultation_date', today())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();
        
        // Lab results needing review
        $pendingLabResults = LabResult::with('patient')
            ->where('status', 'pending')
            ->latest('test_date')
            ->limit(5)
            ->get();
        
        return view('doctor.dashboard', compact(
            'todayPatients',
            'pendingConsultations', 
            'completedToday',
            'pendingLabReviews',
            'recentConsultations',
            'waitingPatients',
            'pendingLabResults'
        ));
    }
    
    /**
     * Show patient queue/waiting list.
     */
    public function patientQueue(): View
    {
        $waitingPatients = Consultation::with('patient')
            ->whereDate('consultation_date', today())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);
        
        return view('doctor.patient-queue', compact('waitingPatients'));
    }
    
    /**
     * Show consultation interface for a specific patient.
     */
    public function consultation(Patient $patient): View
    {
        // Get today's consultation for this patient
        $todayConsultation = Consultation::where('patient_id', $patient->id)
            ->whereDate('consultation_date', today())
            ->first();
        
        // Get patient's medical history
        $patientHistory = Consultation::where('patient_id', $patient->id)
            ->with('labResults')
            ->orderBy('consultation_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent lab results
        $recentLabResults = LabResult::where('patient_id', $patient->id)
            ->orderBy('test_date', 'desc')
            ->limit(5)
            ->get();
        
        return view('doctor.consultation', compact(
            'patient',
            'todayConsultation',
            'patientHistory',
            'recentLabResults'
        ));
    }
    
    /**
     * Update consultation notes and status.
     */
    public function updateConsultation(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'chief_complaint' => 'nullable|string',
            'history_present_illness' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'prescribed_medications' => 'nullable|string',
            'follow_up_instructions' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled'
        ]);
        
        $consultation->update($validated);
        
        return redirect()->back()->with('success', 'Consultation updated successfully.');
    }
    
    /**
     * Show lab results review interface.
     */
    public function labResults(): View
    {
        $pendingResults = LabResult::with('patient')
            ->where('status', 'pending')
            ->orderBy('test_date', 'desc')
            ->paginate(15);
        
        return view('doctor.lab-results', compact('pendingResults'));
    }
    
    /**
     * Review and approve lab result.
     */
    public function reviewLabResult(Request $request, LabResult $labResult)
    {
        $validated = $request->validate([
            'doctor_notes' => 'nullable|string',
            'status' => 'required|in:pending,reviewed,requires_followup'
        ]);
        
        $labResult->update([
            'doctor_notes' => $validated['doctor_notes'],
            'status' => $validated['status'],
            'reviewed_at' => now(),
            'reviewed_by' => 'Dr. ' . auth()->user()->name ?? 'Doctor' // Will be dynamic when auth is implemented
        ]);
        
        return redirect()->back()->with('success', 'Lab result reviewed successfully.');
    }
}
