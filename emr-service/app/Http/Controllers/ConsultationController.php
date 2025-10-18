<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Consultation::with('patient')->orderBy('consultation_date', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('patient', function($subQ) use ($searchTerm) {
                    $subQ->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%");
                })
                ->orWhere('chief_complaint', 'like', "%{$searchTerm}%")
                ->orWhere('assessment', 'like', "%{$searchTerm}%")
                ->orWhere('consultation_notes', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('consultation_date', $request->input('date'));
        }

        $consultations = $query->paginate(20)->withQueryString();

        return view('consultations.index', compact('consultations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        return view('consultations.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'doctor_name' => 'nullable|string|max:255',
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

        // Set default values for API calls
        $consultationData['status'] = $validated['status'] ?? 'pending';
        $consultationData['bp'] = $validated['bp'] ?? '0/0';
        $consultationData['temparature'] = $validated['temparature'] ?? 0;
        $consultationData['weight'] = $validated['weight'] ?? 0;
        $consultationData['o2'] = $validated['o2'] ?? 0;
        $consultationData['pr'] = $validated['pr'] ?? 0;

        $consultation = Consultation::create($consultationData);

        // Return JSON for API requests, redirect for web requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'consultation_date' => $consultation->consultation_date,
                'chief_complaint' => $consultation->chief_complaint,
                'status' => $consultation->status,
                'created_at' => $consultation->created_at,
                'updated_at' => $consultation->updated_at
            ], 201);
        }

        return redirect()->route('consultations.index')->with('success', 'Consultation created successfully.');
    }

    /**
     * Find consultation for doctor assignment (POS billing workflow)
     * Looks for consultation created today for patient, regardless of doctor assignment
     */
    public function findForAssignment(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        // Find consultation for this patient on this date (created by medical staff, no doctor yet)
        $consultation = Consultation::where('patient_id', $validated['patient_id'])
            ->whereDate('consultation_date', $validated['date'])
            ->first();

        $found = $consultation !== null;

        return response()->json([
            'found' => $found,
            'consultation' => $found ? [
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'doctor_name' => $consultation->doctor_name,
                'consultation_date' => $consultation->consultation_date,
                'status' => $consultation->status,
                'chief_complaint' => $consultation->chief_complaint,
                'created_at' => $consultation->created_at
            ] : null
        ]);
    }

    /**
     * Assign doctor to an existing consultation (POS billing workflow)
     */
    public function assignDoctor(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'doctor_name' => 'required|string|max:255',
            'transaction_id' => 'nullable|string'
        ]);

        // Update consultation with doctor assignment
        $consultation->doctor_name = $validated['doctor_name'];
        
        // Append transaction info to notes if provided
        if (isset($validated['transaction_id'])) {
            $existingNotes = $consultation->consultation_notes ?? '';
            $billingNote = "Billed via transaction: {$validated['transaction_id']}. Assigned to: {$validated['doctor_name']}.";
            $consultation->consultation_notes = trim($existingNotes . "\n" . $billingNote);
        }
        
        $consultation->save();

        return response()->json([
            'success' => true,
            'message' => 'Doctor assigned to consultation successfully',
            'consultation' => [
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'doctor_name' => $consultation->doctor_name,
                'status' => $consultation->status
            ]
        ]);
    }

    /**
     * Check if a consultation exists for a given patient, doctor, and date
     * Used by POS service to prevent duplicate consultations (legacy)
     */
    public function checkExists(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'doctor_name' => 'required|string',
            'date' => 'required|date'
        ]);

        $consultation = Consultation::where('patient_id', $validated['patient_id'])
            ->where('doctor_name', $validated['doctor_name'])
            ->whereDate('consultation_date', $validated['date'])
            ->first();

        $exists = $consultation !== null;

        return response()->json([
            'exists' => $exists,
            'consultation' => $exists ? [
                'id' => $consultation->id,
                'patient_id' => $consultation->patient_id,
                'doctor_name' => $consultation->doctor_name,
                'consultation_date' => $consultation->consultation_date,
                'status' => $consultation->status,
                'created_at' => $consultation->created_at
            ] : null
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Consultation $consultation): View
    {
        $consultation->load('patient');
        
        return view('consultations.show', compact('consultation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consultation $consultation): View
    {
        $consultation->load('patient');
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('consultations.edit', compact('consultation', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consultation $consultation): RedirectResponse
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patient,patient_id',
            'date' => 'required|date',
            'bp' => 'required|string|max:255',
            'temparature' => 'required|numeric|between:30,50',
            'weight' => 'required|numeric|between:1,500',
            'o2' => 'required|integer|between:50,100',
            'pr' => 'required|integer|between:30,200',
            'chief_complaint' => 'required|string',
            'consultation_notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after:date',
            'status' => 'required|in:completed,pending,follow_up_required'
        ]);

        // Status is now determined by the frontend based on form completion and follow-up date

        $consultation->update($validatedData);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consultation $consultation): RedirectResponse
    {
        $consultation->delete();

        return redirect()->route('consultations.index')
            ->with('success', 'Consultation deleted successfully.');
    }

    /**
     * Search consultations.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $consultations = Consultation::with('patient')
            ->when($query, function ($q) use ($query) {
                $q->where('chief_complaint', 'like', "%{$query}%")
                  ->orWhere('assessment', 'like', "%{$query}%")
                  ->orWhere('consultation_notes', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($q) use ($query) {
                      $q->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->orderBy('consultation_date', 'desc')
            ->limit(20)
            ->get();

        return response()->json($consultations);
    }
}
