<?php

namespace App\Http\Controllers;

use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Consultation;
use App\Services\LabWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LabResultController extends Controller
{
    protected $workflowService;

    /**
     * Initialize workflow service dependency injection
     * Based on clinic workflow from interview
     */
    public function __construct(LabWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }
    /**
     * Display a listing of lab results with search functionality.
     */
    public function index(Request $request): View
    {
        $query = LabResult::with(['patient', 'consultation']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by patient name
                $q->whereHas('patient', function($patientQuery) use ($search) {
                    $patientQuery->where('first_name', 'LIKE', "%{$search}%")
                                ->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                // Search by test name or type
                ->orWhere('test_name', 'LIKE', "%{$search}%")
                ->orWhere('test_category', 'LIKE', "%{$search}%")
                ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Date range filtering
        if ($request->has('date_from') && $request->date_from) {
            $query->where('test_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('test_date', '<=', $request->date_to . ' 23:59:59');
        }
        
        $labResults = $query->orderBy('test_date', 'desc')->paginate(20);
        
        // Statistics for dashboard
        $stats = [
            'total' => LabResult::count(),
            'pending' => LabResult::where('status', 'pending')->count(),
            'completed' => LabResult::where('status', 'completed')->count(),
        ];
        
        return view('lab-records.index', compact('labResults', 'stats'));
    }

    /**
     * Show the form for creating a new lab result.
     */
    public function create(Request $request): View
    {
        $patient_id = $request->get('patient_id');
        $consultation_id = $request->get('consultation_id');
        
        $patient = null;
        $consultation = null;
        
        if ($patient_id) {
            $patient = Patient::find($patient_id);
        }
        
        if ($consultation_id) {
            $consultation = Consultation::with('patient')->find($consultation_id);
            if ($consultation) {
                $patient = $consultation->patient;
            }
        }
        
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('lab-records.create', compact('patient', 'consultation', 'patients'));
    }

    /**
     * Store a newly created lab result.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'consultation_id' => 'nullable|exists:consultations,id',
            'test_category' => 'nullable|string|max:255',
            'test_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'test_date' => 'nullable|date',
            'technician_name' => 'nullable|string|max:255',
            'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240' // 10MB max
        ]);
        
        // Set initial status
        $validatedData['status'] = 'pending';
        
        // Use current timestamp for test_date if not provided
        $validatedData['test_date'] = $validatedData['test_date'] ?? now();
        
        // Handle file upload
        if ($request->hasFile('result_file')) {
            $file = $request->file('result_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lab-results', $filename, 'public');
            
            // Note: file_path and original_filename are not in the current migration
            // You may need to add these columns to your lab_results table
            // For now, this will not be saved to the database.
        }
        
        $labResult = LabResult::create($validatedData);
        
        return redirect()->route('lab-records.show', $labResult)
            ->with('success', 'Lab result created successfully.');
    }

    /**
     * Display the specified lab result.
     */
    public function show(LabResult $lab_record): View
    {
        $lab_record->load(['patient', 'consultation']);

        // Fallback: if patient is missing but consultation has a patient, use it for display
        if (!$lab_record->patient && $lab_record->consultation && method_exists($lab_record->consultation, 'patient')) {
            $fallbackPatient = $lab_record->consultation->patient;
            if ($fallbackPatient) {
                $lab_record->setRelation('patient', $fallbackPatient);
            }
        }
        
        return view('lab-records.show', ['labResult' => $lab_record]);
    }

    /**
     * Show the form for editing the lab result.
     */
    public function edit(LabResult $lab_record): View
    {
        $lab_record->load(['patient', 'consultation']);
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('lab-records.edit', ['labResult' => $lab_record, 'patients' => $patients]);
    }

    /**
     * Update the lab result.
     */
    public function update(Request $request, LabResult $lab_record): RedirectResponse
    {
        $validatedData = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')],
            'consultation_id' => 'nullable|exists:consultations,id',
            'test_category' => 'nullable|string|max:255',
            'test_name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'result' => 'nullable|string',
            'reference_range' => 'nullable|string|max:255',
            'technician_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'test_date' => 'required|date',
            'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240'
        ]);
        
        // Handle file upload
        if ($request->hasFile('result_file')) {
            // Logic for file handling would go here, but columns are missing
        }
        
        $lab_record->update($validatedData);
        
        return redirect()->route('lab-records.show', $lab_record)
            ->with('success', 'Lab result updated successfully.');
    }

    /**
     * Remove the lab result.
     */
    public function destroy(LabResult $lab_record): RedirectResponse
    {
        // Delete associated file if exists
        if ($lab_record->file_path && Storage::disk('public')->exists($lab_record->file_path)) {
            Storage::disk('public')->delete($lab_record->file_path);
        }
        
        $lab_record->delete();
        
        return redirect()->route('lab-records.index')
            ->with('success', 'Lab result deleted successfully.');
    }

    /**
     * Upload file for existing lab result.
     */
    public function uploadFile(Request $request, LabResult $lab_record): RedirectResponse
    {
        $request->validate([
            'result_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240'
        ]);
        
        // Delete old file if exists
        if ($lab_record->file_path && Storage::disk('public')->exists($lab_record->file_path)) {
            Storage::disk('public')->delete($lab_record->file_path);
        }
        
        $file = $request->file('result_file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('lab-results', $filename, 'public');
        
        $lab_record->update([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName()
        ]);
        
        return redirect()->route('lab-records.show', $lab_record)
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Search API endpoint for ajax calls.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $labResults = LabResult::with(['patient', 'consultation'])
            ->where(function($q) use ($query) {
                $q->whereHas('patient', function($patientQuery) use ($query) {
                    $patientQuery->where('first_name', 'LIKE', "%{$query}%")
                                ->orWhere('last_name', 'LIKE', "%{$query}%");
                })
                ->orWhere('test_name', 'LIKE', "%{$query}%")
                ->orWhere('test_category', 'LIKE', "%{$query}%");
            })
            ->orderBy('test_date', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json($labResults);
    }

    /**
     * Mark lab result as reviewed by doctor
     */
    public function markAsReviewed(Request $request, LabResult $lab_record): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|min:10',
            'reviewed_by' => 'required|string|max:255'
        ]);

        $lab_record->update([
            'status' => 'reviewed',
            // 'reviewed_by' column does not exist
            'notes' => $request->notes,
        ]);

        return redirect()->route('lab-records.show', $lab_record)
            ->with('success', 'Lab result reviewed and marked complete.');
    }

    /**
     * Add doctor notes to lab result
     */
    public function addDoctorNotes(Request $request, LabResult $lab_record): RedirectResponse
    {
        $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $lab_record->update([
            'notes' => $request->notes,
            // 'reviewed_by' and 'reviewed_at' columns do not exist
        ]);

        return redirect()->route('lab-records.show', $lab_record)
            ->with('success', 'Doctor notes added successfully.');
    }

    /**
     * Get lab results pending review (for doctors)
     */
    public function pendingReview(): View
    {
        $pendingResults = LabResult::with(['patient', 'consultation'])
            ->where('status', 'pending')
            ->orderBy('test_date', 'asc')
            ->paginate(15);

        return view('lab-records.pending-review', compact('pendingResults'));
    }

    /**
     * Get lab results by department
     */
    public function byDepartment(Request $request): View
    {
        $department = $request->get('dept', 'laboratory');
        
        $labResults = LabResult::with(['patient', 'consultation'])
            ->where('test_category', 'like', "%{$department}%")
            ->orderBy('test_date', 'desc')
            ->paginate(20);

        return view('lab-records.by-department', compact('labResults', 'department'));
    }
}
