@extends('layouts.app')

@section('title', 'Edit Lab Result')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Edit Lab Result</h1>
            <small class="text-muted">{{ $labResult->patient?->full_name ?? 'Unknown Patient' }} - {{ $labResult->test_name }} on 
                @if($labResult->test_date)
                    {{ $labResult->test_date->format('F d, Y') }}
                @else
                    No date recorded
                @endif
            </small>
        </div>
        <div>
            <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-eye me-1"></i>View Details
            </a>
            <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 bg-warning text-dark">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle me-3 p-2">
                            <i class="bi bi-pencil text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Edit Lab Result</h5>
                            <small>Update test information and results</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('lab-records.update', $labResult) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Patient Selection Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-person me-2"></i>Patient Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Patient *</label>
                                <select name="patient_id" class="form-select form-select-lg @error('patient_id') is-invalid @enderror" required>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->patient_id }}" {{ (old('patient_id', $labResult->patient_id) == $p->patient_id) ? 'selected' : '' }}>
                                            {{ $p->full_name }} (ID: {{ $p->patient_id }}) - {{ $p->age }}yrs, {{ $p->sex }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Related Consultation (Optional)</label>
                                <select name="consultation_id" class="form-select form-select-lg @error('consultation_id') is-invalid @enderror">
                                    <option value="">No consultation - Walk-in test</option>
                                    @if($labResult->consultation)
                                        <option value="{{ $labResult->consultation->consultation_id }}" {{ old('consultation_id', $labResult->consultation_id) == $labResult->consultation->consultation_id ? 'selected' : '' }}>
                                            Consultation on 
                                            @if($labResult->consultation && $labResult->consultation->consultation_date)
                                                {{ $labResult->consultation->consultation_date->format('M d, Y') }}
                                            @else
                                                No date
                                            @endif
                                            - {{ $labResult->consultation->chief_complaint ?? 'No complaint recorded' }}
                                        </option>
                                    @endif
                                </select>
                                @error('consultation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Test Information Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-clipboard-data me-2"></i>Test Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Test Type *</label>
                                <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="Laboratory" {{ old('type', $labResult->type) == 'Laboratory' ? 'selected' : '' }}>Laboratory Tests</option>
                                    <option value="X-ray" {{ old('type', $labResult->type) == 'X-ray' ? 'selected' : '' }}>X-ray Imaging</option>
                                    <option value="ECG" {{ old('type', $labResult->type) == 'ECG' ? 'selected' : '' }}>ECG/EKG</option>
                                    <option value="Ultrasound" {{ old('type', $labResult->type) == 'Ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                                    <option value="CT Scan" {{ old('type', $labResult->type) == 'CT Scan' ? 'selected' : '' }}>CT Scan</option>
                                    <option value="MRI" {{ old('type', $labResult->type) == 'MRI' ? 'selected' : '' }}>MRI</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Specific Test Name *</label>
                                <input type="text" 
                                       name="test_name" 
                                       class="form-control form-control-lg @error('test_name') is-invalid @enderror" 
                                       value="{{ old('test_name', $labResult->test_name) }}" 
                                       placeholder="e.g., CBC with Diff, Chest X-ray PA"
                                       required>
                                @error('test_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Priority *</label>
                                <select name="priority" class="form-select form-select-lg @error('priority') is-invalid @enderror" required>
                                    <option value="routine" {{ old('priority', $labResult->priority) == 'routine' ? 'selected' : '' }}>Routine</option>
                                    <option value="urgent" {{ old('priority', $labResult->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="stat" {{ old('priority', $labResult->priority) == 'stat' ? 'selected' : '' }}>STAT (Immediate)</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Test Status *</label>
                                <select name="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $labResult->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status', $labResult->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $labResult->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="ready_for_review" {{ old('status', $labResult->status) == 'ready_for_review' ? 'selected' : '' }}>Ready for Review</option>
                                    <option value="reviewed" {{ old('status', $labResult->status) == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Test Date & Time *</label>
                                <input type="datetime-local" 
                                       name="date" 
                                       class="form-control form-control-lg @error('date') is-invalid @enderror" 
                                       value="{{ old('date', $labResult->test_date ? $labResult->test_date->format('Y-m-d\TH:i') : '') }}"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Test Description (Optional)</label>
                                <input type="text" 
                                       name="test_description" 
                                       class="form-control form-control-lg @error('test_description') is-invalid @enderror" 
                                       value="{{ old('test_description', $labResult->test_description) }}" 
                                       placeholder="Additional details about the test">
                                @error('test_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Results Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-clipboard-check me-2"></i>Test Results
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Result Value</label>
                                <input type="text" 
                                       name="result_value" 
                                       class="form-control form-control-lg @error('result_value') is-invalid @enderror" 
                                       value="{{ old('result_value', $labResult->result_value) }}" 
                                       placeholder="e.g., 120, Normal, Positive">
                                @error('result_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reference Range</label>
                                <input type="text" 
                                       name="reference_range" 
                                       class="form-control form-control-lg @error('reference_range') is-invalid @enderror" 
                                       value="{{ old('reference_range', $labResult->reference_range) }}" 
                                       placeholder="e.g., 80-120, Normal, Negative">
                                @error('reference_range')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Result Notes</label>
                                <textarea name="result_notes" 
                                          class="form-control form-control-lg @error('result_notes') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Detailed result findings, interpretations, recommendations...">{{ old('result_notes', $labResult->result_notes) }}</textarea>
                                @error('result_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Staff Information Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-people me-2"></i>Staff Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Requested By *</label>
                                <input type="text" 
                                       name="requested_by" 
                                       class="form-control form-control-lg @error('requested_by') is-invalid @enderror" 
                                       value="{{ old('requested_by', $labResult->requested_by) }}" 
                                       placeholder="Doctor or staff name"
                                       required>
                                @error('requested_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Performed By</label>
                                <input type="text" 
                                       name="performed_by" 
                                       class="form-control form-control-lg @error('performed_by') is-invalid @enderror" 
                                       value="{{ old('performed_by', $labResult->performed_by) }}" 
                                       placeholder="Technician name">
                                @error('performed_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Reviewed By</label>
                                <input type="text" 
                                       name="reviewed_by" 
                                       class="form-control form-control-lg @error('reviewed_by') is-invalid @enderror" 
                                       value="{{ old('reviewed_by', $labResult->reviewed_by) }}" 
                                       placeholder="Doctor name">
                                @error('reviewed_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Technician Notes</label>
                                <textarea name="technician_notes" 
                                          class="form-control form-control-lg @error('technician_notes') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Notes from the technician who performed the test...">{{ old('technician_notes', $labResult->technician_notes) }}</textarea>
                                @error('technician_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Notes</label>
                                <textarea name="doctor_notes" 
                                          class="form-control form-control-lg @error('doctor_notes') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Notes from the reviewing doctor...">{{ old('doctor_notes', $labResult->doctor_notes) }}</textarea>
                                @error('doctor_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- File Upload Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-warning border-bottom pb-2 mb-3">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Result File
                                </h6>
                            </div>
                        </div>
                        
                        @if($labResult->file_path)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong>Current File:</strong> {{ $labResult->original_filename }}
                                            <div class="mt-1">
                                                <a href="{{ Storage::url($labResult->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="bi bi-eye me-1"></i>View Current File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    @if($labResult->file_path)
                                        Replace Result File (Optional)
                                    @else
                                        Upload Result File (Optional)
                                    @endif
                                </label>
                                <input type="file" 
                                       name="result_file" 
                                       class="form-control form-control-lg @error('result_file') is-invalid @enderror"
                                       accept=".pdf,.jpg,.jpeg,.png,.dcm">
                                <small class="text-muted">
                                    Supported formats: PDF, JPG, PNG, DICOM (DCM). Maximum size: 10MB
                                    @if($labResult->file_path)
                                        <br><strong>Note:</strong> Uploading a new file will replace the current file.
                                    @endif
                                </small>
                                @error('result_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <div>
                                        @if($labResult->file_path || $labResult->result_notes || $labResult->result_value)
                                            <button type="button" class="btn btn-danger btn-lg me-2" onclick="confirmDelete()">
                                                <i class="bi bi-trash me-2"></i>Delete Lab Result
                                            </button>
                                        @endif
                                        <button type="submit" class="btn btn-warning btn-lg">
                                            <i class="bi bi-check-circle me-2"></i>Update Lab Result
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="card mt-3 border-info">
                <div class="card-body">
                    <h6 class="text-info">
                        <i class="bi bi-info-circle me-2"></i>Edit Guidelines
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Update status to "Ready for Review" when results are complete</li>
                        <li>Mark as "Reviewed" only after doctor has examined the results</li>
                        <li>Add result values and reference ranges for quantitative tests</li>
                        <li>Include detailed notes for qualitative or image-based tests</li>
                        <li>Upload or replace result files as needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Lab Result
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this lab result?</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This action cannot be undone. All associated files and data will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('lab-records.destroy', $labResult) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Lab Result
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<!-- Enhanced Senior-Friendly Styles -->
<style>
.form-control-lg, .form-select-lg {
    font-size: 1.1em !important;
    padding: 0.75rem 1rem !important;
    border-radius: 0.5rem !important;
    border: 2px solid #dee2e6 !important;
    transition: all 0.3s ease;
    background-color: #fff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control-lg:focus, .form-select-lg:focus {
    border-color: #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.3) !important;
    transform: scale(1.02);
}

.btn-lg {
    font-size: 1.1em;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
}

.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

.form-label {
    margin-bottom: 0.75rem;
    font-weight: 600;
}

textarea.form-control-lg {
    resize: vertical;
}

.alert {
    border-radius: 0.5rem;
}
</style>
@endsection
