<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\MedicalBill;

class PatientsController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search by name, code, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by gender if provided
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by insurance provider if provided
        if ($request->filled('insurance')) {
            $query->where('insurance_provider', 'like', "%{$request->insurance}%");
        }

        $patients = $query->orderBy('first_name')->orderBy('last_name')->paginate(20);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        Patient::create([
            'patient_code' => Patient::generatePatientCode(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'insurance_provider' => $request->insurance_provider,
            'insurance_number' => $request->insurance_number,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully!');
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        // Load patient's medical bills with related data
        $medicalBills = MedicalBill::where('patient_id', $patient->id)
            ->with('items.medicalService', 'payments')
            ->orderBy('bill_date', 'desc')
            ->paginate(10);

        // Calculate patient statistics
        $totalBills = $medicalBills->total();
        $totalAmount = MedicalBill::where('patient_id', $patient->id)->sum('total_amount');
        $pendingAmount = MedicalBill::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->sum('total_amount');

        return view('patients.show', compact('patient', 'medicalBills', 'totalBills', 'totalAmount', 'pendingAmount'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $patient->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'insurance_provider' => $request->insurance_provider,
            'insurance_number' => $request->insurance_number,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient information updated successfully!');
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        // Check if patient has any medical bills
        if ($patient->medicalBills()->count() > 0) {
            return redirect()->route('patients.index')
                ->with('error', 'Cannot delete patient with existing medical bills.');
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully!');
    }

    /**
     * Search patients (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');
        
        $patients = Patient::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orWhere('patient_code', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'patient_code', 'first_name', 'last_name', 'phone']);

        return response()->json($patients);
    }

    /**
     * Get patient details (AJAX endpoint)
     */
    public function getDetails($id)
    {
        $patient = Patient::find($id);
        
        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Get patient bills summary
     */
    public function getBillsSummary(Patient $patient)
    {
        $summary = [
            'total_bills' => $patient->medicalBills()->count(),
            'total_amount' => $patient->medicalBills()->sum('total_amount'),
            'pending_amount' => $patient->medicalBills()->where('status', 'pending')->sum('total_amount'),
            'paid_amount' => $patient->medicalBills()->where('status', 'paid')->sum('total_amount'),
        ];

        return response()->json($summary);
    }
}
