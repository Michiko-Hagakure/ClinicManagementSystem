<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
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
        // Get logged-in doctor's name from session
        $doctorName = session('user_name') ?? session('name');
        
        // Get patient IDs for this doctor's consultations
        $patientIds = Consultation::where('doctor_name', $doctorName)
            ->distinct()
            ->pluck('patient_id')
            ->toArray();
        
        // Doctor-specific statistics (filtered by doctor's assigned patients)
        $todayPatients = Consultation::where('doctor_name', $doctorName)
            ->whereDate('consultation_date', today())
            ->distinct('patient_id')
            ->count('patient_id');
        $pendingConsultations = Consultation::where('doctor_name', $doctorName)
            ->where('status', 'pending')
            ->count();
        $completedToday = Consultation::where('doctor_name', $doctorName)
            ->whereDate('consultation_date', today())
            ->where('status', 'completed')
            ->count();
        $pendingLabReviews = LabResult::whereIn('patient_id', $patientIds)
            ->where('status', 'pending')
            ->count();
        
        // Recent consultations for this doctor only
        $recentConsultations = Consultation::with('patient')
            ->where('doctor_name', $doctorName)
            ->latest('consultation_date')
            ->limit(5)
            ->get();
        
        // Patients waiting (pending consultations from today for this doctor)
        $waitingPatients = Consultation::with('patient')
            ->where('doctor_name', $doctorName)
            ->whereDate('consultation_date', today())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();
        
        // Lab results needing review (only for this doctor's patients)
        $pendingLabResults = LabResult::with('patient')
            ->whereIn('patient_id', $patientIds)
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
        // Get logged-in doctor's name from session
        $doctorName = session('user_name') ?? session('name');
        
        $waitingPatients = Consultation::with('patient')
            ->where('doctor_name', $doctorName)
            ->whereDate('consultation_date', today())
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);
        
        return view('doctor.patient-queue', compact('waitingPatients'));
    }
    
    /**
     * Show patient records from doctor perspective.
     */
    public function patientRecords(Request $request): View
    {
        // Get logged-in doctor's name from session
        $doctorName = session('user_name') ?? session('name');
        
        // Get patient IDs that have consultations assigned to this doctor
        $patientIds = Consultation::where('doctor_name', $doctorName)
            ->distinct()
            ->pluck('patient_id')
            ->toArray();
        
        // Filter patients to only show those assigned to this doctor
        $query = Patient::whereIn('id', $patientIds);
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                  
                // Search by ID if numeric
                if (is_numeric($searchTerm)) {
                    $q->orWhere('id', $searchTerm);
                }
            });
        }
        
        $patients = $query->latest('id')->paginate(15);
        
        return view('doctor.patient-records', compact('patients'));
    }
    
    /**
     * Show doctor's consultations from doctor perspective.
     */
    public function myConsultations(Request $request): View
    {
        // Get logged-in doctor's name from session
        $doctorName = session('user_name') ?? session('name');
        
        // Filter consultations by this doctor
        $query = Consultation::with('patient')
            ->where('doctor_name', $doctorName);
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('patient', function($patientQuery) use ($searchTerm) {
                    $patientQuery->where('first_name', 'like', "%{$searchTerm}%")
                               ->orWhere('last_name', 'like', "%{$searchTerm}%")
                               ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                               
                    // Search by patient ID if numeric
                    if (is_numeric($searchTerm)) {
                        $patientQuery->orWhere('id', $searchTerm);
                    }
                })
                ->orWhere('chief_complaint', 'like', "%{$searchTerm}%")
                ->orWhere('consultation_notes', 'like', "%{$searchTerm}%")
                ->orWhere('assessment', 'like', "%{$searchTerm}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Handle date filter
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('consultation_date', $request->date);
        }
        
        $consultations = $query->latest('consultation_date')->paginate(15);
        
        return view('doctor.my-consultations', compact('consultations'));
    }
    
    /**
     * View patient details from doctor's perspective.
     */
    public function viewPatient(Patient $patient): View
    {
        // Get patient's consultations
        $consultations = Consultation::where('patient_id', $patient->id)
            ->orderBy('consultation_date', 'desc')
            ->get();
        
        // Get patient's lab results
        $labResults = LabResult::where('patient_id', $patient->id)
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('doctor.view-patient', compact('patient', 'consultations', 'labResults'));
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
     * Status is automatically set to 'completed' when required fields are filled.
     */
    public function updateConsultation(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'chief_complaint' => 'nullable|string',
            'patient_history_notes' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'assessment' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'medications_prescribed' => 'nullable|string',
            'patient_instructions' => 'nullable|string',
            'status' => 'nullable|in:pending,completed,cancelled'
        ]);
        
        // Automatically determine status based on completion of required fields
        // Required fields: chief_complaint and assessment (diagnosis)
        if (!empty($validated['chief_complaint']) && !empty($validated['assessment'])) {
            $validated['status'] = 'completed';
        } elseif (!isset($validated['status'])) {
            // If status not explicitly set and required fields missing, keep as pending
            $validated['status'] = 'pending';
        }
        
        $consultation->update($validated);
        
        $statusMessage = $validated['status'] === 'completed' 
            ? 'Consultation completed and saved successfully!' 
            : 'Consultation updated successfully.';
        
        return redirect()->back()->with('success', $statusMessage);
    }
    
    /**
     * Show lab results review and upload interface.
     */
    public function labResults(): View
    {
        // Get logged-in doctor's name from session
        $doctorName = session('user_name') ?? session('name');
        
        // Get patient IDs that have consultations assigned to this doctor
        $patientIds = Consultation::where('doctor_name', $doctorName)
            ->distinct()
            ->pluck('patient_id')
            ->toArray();
        
        // Get pending results that need review (only for this doctor's patients)
        $pendingResults = LabResult::with('patient')
            ->whereIn('patient_id', $patientIds)
            ->where('status', 'pending')
            ->orderBy('test_date', 'desc')
            ->paginate(15, ['*'], 'pending_page');
        
        // Get recent completed/uploaded results (last 30 days, only for this doctor's patients)
        $completedResults = LabResult::with('patient')
            ->whereIn('patient_id', $patientIds)
            ->whereIn('status', ['reviewed', 'requires_followup'])
            ->where('test_date', '>=', now()->subDays(30))
            ->orderBy('test_date', 'desc')
            ->paginate(15, ['*'], 'completed_page');
        
        // Get patients for upload dropdown (only this doctor's patients)
        $patients = Patient::whereIn('id', $patientIds)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        
        return view('doctor.lab-results', compact('pendingResults', 'completedResults', 'patients'));
    }
    
    /**
     * Upload new lab results or X-rays.
     */
    public function uploadLabResult(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'test_type' => 'required|string|max:255',
            'test_category' => 'nullable|string|max:255',
            'test_date' => 'required|date',
            'results' => 'nullable|string',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max per file
            'notes' => 'nullable|string'
        ]);

        try {
            // Create the lab result record
            $labResult = LabResult::create([
                'patient_id' => $validated['patient_id'],
                'test_name' => $validated['test_type'], // Set the required test_name field
                'test_type' => $validated['test_type'], // Also set the new test_type field
                'test_category' => $validated['test_category'] ?? 'Doctor Upload',
                'test_date' => now(), // Use current timestamp instead of form date
                'result' => $validated['results'] ?? 'File(s) uploaded - see attachments', // Original field
                'results' => $validated['results'] ?? 'File(s) uploaded - see attachments', // New field
                'status' => 'pending', // All uploads need formal review, even doctor uploads
                'doctor_notes' => $validated['notes'],
                'reviewed_at' => null, // Will be set when formally reviewed
                'reviewed_by' => null // Will be set when formally reviewed
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                $uploadPath = storage_path('app/public/lab-results');
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fileNames = [];
                foreach ($request->file('files') as $index => $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = "lab_{$labResult->id}_{$index}_" . time() . "_{$originalName}";
                    
                    // Move file to storage
                    $file->move($uploadPath, $fileName);
                    $fileNames[] = $fileName;
                }

                // Update lab result with file information
                $updatedResults = ($validated['results'] ?? '') . "\n\nAttached Files: " . implode(', ', array_map(function($f) {
                    return pathinfo($f, PATHINFO_FILENAME);
                }, $fileNames));
                
                $labResult->update([
                    'file_attachments' => $fileNames, // Laravel will auto-encode as JSON due to array cast
                    'result' => $updatedResults, // Original field
                    'results' => $updatedResults // New field
                ]);
            }

            return redirect()->back()->with('success', 'Lab result uploaded successfully for ' . $labResult->patient->full_name . '! It now appears in "Review Results" for formal review.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload lab result: ' . $e->getMessage());
        }
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
    
    /**
     * Show consultation creation form for doctors.
     */
    public function createConsultation(): View
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        return view('consultations.create', compact('patients'));
    }
    
    /**
     * Store consultation created by doctor.
     */
    public function storeConsultation(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'date' => 'required|date',
            'bp' => 'nullable|string|max:20',
            'temparature' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'o2' => 'nullable|numeric',
            'pr' => 'nullable|numeric',
            'chief_complaint' => 'nullable|string',
            'consultation_notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $consultationData = $validated;
        $consultationData['consultation_date'] = $validated['date'];
        unset($consultationData['date']);

        // Set default values
        $consultationData['status'] = $validated['status'] ?? 'pending';
        $consultationData['bp'] = $validated['bp'] ?? '0/0';
        $consultationData['temparature'] = $validated['temparature'] ?? 0;
        $consultationData['weight'] = $validated['weight'] ?? 0;
        $consultationData['o2'] = $validated['o2'] ?? 0;
        $consultationData['pr'] = $validated['pr'] ?? 0;

        $consultation = Consultation::create($consultationData);

        return redirect()->route('doctor.my-consultations')->with('success', 'Consultation created successfully.');
    }
}
