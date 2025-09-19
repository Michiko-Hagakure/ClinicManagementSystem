<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if this is an API request for Select2
        if ($request->ajax() || $request->wantsJson()) {
            $query = $request->input('q');

            $patients = Patient::where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('phone_number', 'LIKE', "%{$query}%");

                if (is_numeric($query)) {
                    $q->orWhere('id', $query);
                }
            })->limit(10)->get();

            // Format for Select2
            return response()->json($patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'text' => $patient->full_name . ' (ID: ' . $patient->id . ') - ' . $patient->age . ' yrs',
                ];
            }));
        }

        // Original code for web view
        $queryBuilder = Patient::query();

        // Search functionality for web view
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $queryBuilder->where(function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }

        $patients = $queryBuilder->orderBy('id', 'desc')->paginate(20);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'civil_status' => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
        ]);

        // Manually map contact_number to phone_number
        $validated['phone_number'] = $validated['contact_number'];
        unset($validated['contact_number']);

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient registered successfully! 🎉');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $patient->load(['consultations.labResults', 'labResults']);
        
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'civil_status' => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
        ]);

        // Manually map contact_number to phone_number
        $validated['phone_number'] = $validated['contact_number'];
        unset($validated['contact_number']);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient information updated successfully! ✅');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
                        ->with('success', 'Patient record deleted successfully.');
    }

    /**
     * Get patient consultations
     */
    public function consultations(Patient $patient)
    {
        $consultations = $patient->consultations()->with('labResults')->orderBy('date', 'desc')->get();
        
        return view('patients.consultations', compact('patient', 'consultations'));
    }

    /**
     * Get patient lab results
     */
    public function labResults(Patient $patient)
    {
        $labResults = $patient->labResults()->with('consultation')->orderBy('date', 'desc')->get();
        
        return view('patients.lab-results', compact('patient', 'labResults'));
    }

    /**
     * API: Search patients (for inter-service communication)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $patients = Patient::where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('phone_number', 'LIKE', "%{$query}%");

            if (is_numeric($query)) {
                $q->orWhere('id', $query);
            }
        })->limit(10)->get();

        return response()->json($patients->map(function ($patient) {
            return [
                'id' => $patient->id,
                'patient_code' => 'P' . str_pad($patient->id, 4, '0', STR_PAD_LEFT),
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'full_name' => $patient->full_name,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'phone' => $patient->phone_number,
                'age' => $patient->age,
                'civil_status' => $patient->civil_status,
                'address' => $patient->address ?? '',
                'created_at' => $patient->created_at,
                'updated_at' => $patient->updated_at
            ];
        }));
    }
}
