@extends('layouts.app')

@section('title', 'New Lab Test')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">New Lab Test Order</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Request New Laboratory Test</small>
        </div>
        <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Lab Records
        </a>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle me-3 p-2">
                            <i class="bi bi-file-earmark-medical text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Lab Test Order Form</h5>
                            <small>Create new laboratory test record</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('lab-records.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Patient Selection Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-person me-2"></i>Patient Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Patient *</label>
                                <select name="patient_id" class="form-select form-select-lg @error('patient_id') is-invalid @enderror" required>
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->patient_id }}" {{ (old('patient_id') == $p->patient_id || ($patient && $patient->patient_id == $p->patient_id)) ? 'selected' : '' }}>
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
                                    @if($consultation)
                                        <option value="{{ $consultation->consultation_id }}" selected>
                                            Consultation on 
                                            @if($consultation->consultation_date)
                                                {{ $consultation->consultation_date->format('M d, Y') }}
                                            @else
                                                No date
                                            @endif
                                            - {{ $consultation->chief_complaint ?? 'No complaint recorded' }}
                                        </option>
                                    @endif
                                </select>
                                <small class="text-muted">Leave blank for walk-in lab tests</small>
                                @error('consultation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Test Information Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-clipboard-data me-2"></i>Test Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Test Type *</label>
                                <select name="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">Select Test Type</option>
                                    <option value="Laboratory" {{ old('type') == 'Laboratory' ? 'selected' : '' }}>Laboratory Tests</option>
                                    <option value="X-ray" {{ old('type') == 'X-ray' ? 'selected' : '' }}>X-ray Imaging</option>
                                    <option value="ECG" {{ old('type') == 'ECG' ? 'selected' : '' }}>ECG/EKG</option>
                                    <option value="Ultrasound" {{ old('type') == 'Ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                                    <option value="CT Scan" {{ old('type') == 'CT Scan' ? 'selected' : '' }}>CT Scan</option>
                                    <option value="MRI" {{ old('type') == 'MRI' ? 'selected' : '' }}>MRI</option>
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
                                       value="{{ old('test_name') }}" 
                                       placeholder="e.g., CBC with Diff, Chest X-ray PA"
                                       required>
                                @error('test_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Priority *</label>
                                <select name="priority" class="form-select form-select-lg @error('priority') is-invalid @enderror" required>
                                    <option value="routine" {{ old('priority', 'routine') == 'routine' ? 'selected' : '' }}>Routine</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="stat" {{ old('priority') == 'stat' ? 'selected' : '' }}>STAT (Immediate)</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Test Date & Time *</label>
                                <input type="datetime-local" 
                                       name="date" 
                                       class="form-control form-control-lg @error('date') is-invalid @enderror" 
                                       value="{{ old('date', now()->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Test Description (Optional)</label>
                                <input type="text" 
                                       name="test_description" 
                                       class="form-control form-control-lg @error('test_description') is-invalid @enderror" 
                                       value="{{ old('test_description') }}" 
                                       placeholder="Additional details about the test">
                                @error('test_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Staff Information Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-people me-2"></i>Staff Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Requested By *</label>
                                <input type="text" 
                                       name="requested_by" 
                                       class="form-control form-control-lg @error('requested_by') is-invalid @enderror" 
                                       value="{{ old('requested_by') }}" 
                                       placeholder="Doctor or staff name who requested this test"
                                       required>
                                @error('requested_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Initial Notes</label>
                                <textarea name="technician_notes" 
                                          class="form-control form-control-lg @error('technician_notes') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Any initial notes or special instructions...">{{ old('technician_notes') }}</textarea>
                                @error('technician_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- File Upload Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Result File Upload (Optional)
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Upload Test Result File</label>
                                <input type="file" 
                                       name="result_file" 
                                       class="form-control form-control-lg @error('result_file') is-invalid @enderror"
                                       accept=".pdf,.jpg,.jpeg,.png,.dcm">
                                <small class="text-muted">
                                    Supported formats: PDF, JPG, PNG, DICOM (DCM). Maximum size: 10MB
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
                                    <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-file-earmark-plus me-2"></i>Create Lab Test
                                    </button>
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
                        <i class="bi bi-info-circle me-2"></i>Lab Test Guidelines
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Select the patient and test type carefully</li>
                        <li>Use STAT priority only for immediate/emergency tests</li>
                        <li>Link to consultation when test is ordered during patient visit</li>
                        <li>Leave consultation blank for walk-in lab tests</li>
                        <li>Upload result files immediately when available</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

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
    border-color: #0d6efd !important;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.3) !important;
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
</style>
@endsection
