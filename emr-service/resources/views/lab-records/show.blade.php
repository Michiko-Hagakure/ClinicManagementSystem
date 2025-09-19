@extends('layouts.app')

@section('title', 'Lab Result Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Lab Result Details</h1>
            <small class="text-muted">{{ $labResult->patient->full_name }} - {{ $labResult->test_name }} on 
                @if($labResult->test_date)
                    {{ $labResult->test_date->format('F d, Y') }}
                @else
                    No date recorded
                @endif
            </small>
        </div>
        <div>
            <a href="{{ route('lab-records.edit', $labResult) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Patient Information Card -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-person me-2"></i>Patient Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="patient-avatar text-center mb-3">
                        <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                    
                    <div class="patient-details">
                        <h5 class="text-center mb-3">{{ $labResult->patient->full_name }}</h5>
                        
                        <div class="info-item mb-2">
                            <strong>Patient ID:</strong>
                            <span class="text-muted">{{ $labResult->patient->patient_id }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Age & Gender:</strong>
                            <span class="text-muted">{{ $labResult->patient->age }} years, {{ $labResult->patient->sex }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Birth Date:</strong>
                            <span class="text-muted">{{ $labResult->patient->birth_date->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Contact:</strong>
                            <span class="text-muted">{{ $labResult->patient->contact_number }}</span>
                        </div>
                        
                        <div class="info-item">
                            <strong>Address:</strong>
                            <span class="text-muted">{{ $labResult->patient->address }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <a href="{{ route('patients.show', $labResult->patient) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-person-lines-fill me-1"></i>View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lab Result Details -->
        <div class="col-md-8">
            <!-- Test Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Test Information
                        </h6>
                        @switch($labResult->status)
                            @case('pending')
                                <span class="badge bg-light text-warning fs-6">
                                    <i class="bi bi-clock me-1"></i>Pending
                                </span>
                                @break
                            @case('in_progress')
                                <span class="badge bg-light text-info fs-6">
                                    <i class="bi bi-arrow-repeat me-1"></i>In Progress
                                </span>
                                @break
                            @case('completed')
                                <span class="badge bg-light text-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>Completed
                                </span>
                                @break
                            @case('ready_for_review')
                                <span class="badge bg-light text-primary fs-6">
                                    <i class="bi bi-eye me-1"></i>Ready for Review
                                </span>
                                @break
                            @case('reviewed')
                                <span class="badge bg-light text-dark fs-6">
                                    <i class="bi bi-check-all me-1"></i>Reviewed
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <strong>Test Type:</strong>
                                <div class="mt-1">
                                    @switch($labResult->type)
                                        @case('Laboratory')
                                            <span class="badge bg-warning fs-6">
                                                <i class="bi bi-droplet me-1"></i>Laboratory
                                            </span>
                                            @break
                                        @case('X-ray')
                                            <span class="badge bg-primary fs-6">
                                                <i class="bi bi-radioactive me-1"></i>X-ray
                                            </span>
                                            @break
                                        @case('ECG')
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-heart-pulse me-1"></i>ECG
                                            </span>
                                            @break
                                        @case('Ultrasound')
                                            <span class="badge bg-info fs-6">
                                                <i class="bi bi-soundwave me-1"></i>Ultrasound
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary fs-6">
                                                <i class="bi bi-file-medical me-1"></i>{{ $labResult->type }}
                                            </span>
                                    @endswitch
                                </div>
                            </div>
                            
                            <div class="info-item mb-3">
                                <strong>Test Name:</strong>
                                <div class="text-primary fw-bold">{{ $labResult->test_name }}</div>
                            </div>
                            
                            @if($labResult->test_description)
                                <div class="info-item mb-3">
                                    <strong>Description:</strong>
                                    <div class="text-muted">{{ $labResult->test_description }}</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <strong>Priority:</strong>
                                <div class="mt-1">
                                    @switch($labResult->priority)
                                        @case('stat')
                                            <span class="badge bg-danger fs-6">STAT</span>
                                            @break
                                        @case('urgent')
                                            <span class="badge bg-warning fs-6">URGENT</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary fs-6">ROUTINE</span>
                                    @endswitch
                                </div>
                            </div>
                            
                            <div class="info-item mb-3">
                                <strong>Test Date & Time:</strong>
                                <div class="text-muted">
                                    @if($labResult->test_date)
                                        {{ $labResult->test_date->format('F d, Y - g:i A') }}
                                    @else
                                        No date/time recorded
                                    @endif
                                </div>
                            </div>
                            
                            @if($labResult->consultation)
                                <div class="info-item mb-3">
                                    <strong>Related Consultation:</strong>
                                    <div>
                                        <a href="{{ route('consultations.show', $labResult->consultation) }}" class="text-decoration-none">
                                            <i class="bi bi-link me-1"></i>
                                            @if($labResult->consultation && $labResult->consultation->consultation_date)
                                                {{ $labResult->consultation->consultation_date->format('M d, Y') }}
                                            @else
                                                No date
                                            @endif - 
                                            {{ Str::limit($labResult->consultation->chief_complaint, 30) }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="info-item mb-3">
                                    <strong>Walk-in Test:</strong>
                                    <div class="text-muted">No consultation linked</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Test Results
                    </h6>
                </div>
                <div class="card-body">
                    @if($labResult->result_notes || $labResult->result_value || $labResult->reference_range)
                        <div class="row">
                            @if($labResult->result_value || $labResult->reference_range)
                                <div class="col-md-6">
                                    @if($labResult->result_value)
                                        <div class="info-item mb-3">
                                            <strong>Result Value:</strong>
                                            <div class="h5 text-primary">{{ $labResult->result_value }}</div>
                                        </div>
                                    @endif
                                    
                                    @if($labResult->reference_range)
                                        <div class="info-item mb-3">
                                            <strong>Reference Range:</strong>
                                            <div class="text-muted">{{ $labResult->reference_range }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($labResult->result_notes)
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong>Result Notes:</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {{ $labResult->result_notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if($labResult->completed_at)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Results completed on {{ $labResult->completed_at->format('F d, Y - g:i A') }}
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-hourglass-split display-4"></i>
                            <h6 class="mt-2">Results Pending</h6>
                            <p>Test results are not yet available.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File Attachment Card -->
            @if($labResult->file_path)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Attached File
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="file-icon me-3">
                                @php
                                    $extension = pathinfo($labResult->original_filename, PATHINFO_EXTENSION);
                                @endphp
                                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                    <i class="bi bi-file-earmark-image display-4 text-success"></i>
                                @elseif(strtolower($extension) === 'pdf')
                                    <i class="bi bi-file-earmark-pdf display-4 text-danger"></i>
                                @else
                                    <i class="bi bi-file-earmark display-4 text-secondary"></i>
                                @endif
                            </div>
                            <div class="file-info flex-grow-1">
                                <h6 class="mb-0">{{ $labResult->original_filename }}</h6>
                                <small class="text-muted">
                                    Uploaded result file
                                </small>
                            </div>
                            <div class="file-actions">
                                <a href="{{ Storage::url($labResult->file_path) }}" 
                                   target="_blank" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>View File
                                </a>
                                <a href="{{ Storage::url($labResult->file_path) }}" 
                                   download="{{ $labResult->original_filename }}"
                                   class="btn btn-outline-success ms-2">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- File Upload Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Result File
                        </h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                        <h6 class="mt-2 text-muted">No file attached</h6>
                        <p class="text-muted">Upload the test result file when available.</p>
                        
                        <form action="{{ route('lab-records.upload', $labResult) }}" method="POST" enctype="multipart/form-data" class="d-inline-block">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="result_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.dcm" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-1"></i>Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Staff Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>Staff Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($labResult->requested_by)
                                <div class="info-item mb-3">
                                    <strong>Requested By:</strong>
                                    <div class="text-muted">{{ $labResult->requested_by }}</div>
                                </div>
                            @endif
                            
                            @if($labResult->performed_by)
                                <div class="info-item mb-3">
                                    <strong>Performed By:</strong>
                                    <div class="text-muted">{{ $labResult->performed_by }}</div>
                                </div>
                            @endif
                            
                            @if($labResult->reviewed_by)
                                <div class="info-item">
                                    <strong>Reviewed By:</strong>
                                    <div class="text-muted">{{ $labResult->reviewed_by }}</div>
                                    @if($labResult->reviewed_at)
                                        <small class="text-muted d-block">
                                            {{ $labResult->reviewed_at->format('M d, Y - g:i A') }}
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            @if($labResult->technician_notes)
                                <div class="info-item mb-3">
                                    <strong>Technician Notes:</strong>
                                    <div class="mt-2 p-3 bg-light rounded">
                                        {{ $labResult->technician_notes }}
                                    </div>
                                </div>
                            @endif
                            
                            @if($labResult->doctor_notes)
                                <div class="info-item">
                                    <strong>Doctor Notes:</strong>
                                    <div class="mt-2 p-3 bg-light rounded">
                                        {{ $labResult->doctor_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
.info-item strong {
    color: #495057;
    font-weight: 600;
}

.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
    padding: 0.5em 0.75em;
}

.display-4 {
    font-size: 2.5rem;
}

.file-icon i {
    font-size: 3rem;
}

.input-group .form-control {
    border-radius: 0.375rem 0 0 0.375rem;
}

.input-group .btn {
    border-radius: 0 0.375rem 0.375rem 0;
}
</style>
@endsection
