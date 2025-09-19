@extends('layouts.app')

@section('title', 'Consultation Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Consultation Details</h1>
            <small class="text-muted">{{ $consultation->patient->full_name }} - 
                @if($consultation->consultation_date)
                    {{ $consultation->consultation_date->format('F d, Y') }}
                @else
                    No date recorded
                @endif
            </small>
        </div>
        <div>
            <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
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
                        <h5 class="text-center mb-3">{{ $consultation->patient->full_name }}</h5>
                        
                        <div class="info-item mb-2">
                            <strong>Patient ID:</strong>
                            <span class="text-muted">{{ $consultation->patient->patient_id }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Age & Gender:</strong>
                            <span class="text-muted">{{ $consultation->patient->age }} years, {{ $consultation->patient->sex }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Birth Date:</strong>
                            <span class="text-muted">{{ $consultation->patient->birth_date->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Contact:</strong>
                            <span class="text-muted">{{ $consultation->patient->contact_number }}</span>
                        </div>
                        
                        <div class="info-item mb-2">
                            <strong>Address:</strong>
                            <span class="text-muted">{{ $consultation->patient->address }}</span>
                        </div>
                        
                        <div class="info-item">
                            <strong>Civil Status:</strong>
                            <span class="text-muted">{{ $consultation->patient->civil_staus }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <a href="{{ route('patients.show', $consultation->patient) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-person-lines-fill me-1"></i>View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consultation Details -->
        <div class="col-md-8">
            <!-- Consultation Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-file-earmark-medical me-2"></i>Consultation Summary
                        </h6>
                        @if($consultation->status == 'completed')
                            <span class="badge bg-light text-success">Completed</span>
                        @elseif($consultation->status == 'pending')
                            <span class="badge bg-light text-warning">Pending</span>
                        @elseif($consultation->status == 'follow_up_required')
                            <span class="badge bg-light text-info">Follow-up Required</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Consultation Date:</strong>
                                <span class="text-muted">
                                    @if($consultation->consultation_date)
                                        {{ $consultation->consultation_date->format('l, F d, Y') }}
                                    @else
                                        No date recorded
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($consultation->follow_up_date)
                                <div class="info-item">
                                    <strong>Follow-up Date:</strong>
                                    <span class="text-primary">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        @if($consultation->follow_up_date)
                                            {{ $consultation->follow_up_date->format('M d, Y') }}
                                        @else
                                            Not specified
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    

                </div>
            </div>

            <!-- Vital Signs Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-heart-pulse me-2"></i>Vital Signs
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="vital-sign">
                                <div class="vital-value">{{ $consultation->bp }}</div>
                                <div class="vital-label">Blood Pressure</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="vital-sign">
                                <div class="vital-value">{{ $consultation->temparature }}°C</div>
                                <div class="vital-label">Temperature</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="vital-sign">
                                <div class="vital-value">{{ $consultation->weight }} kg</div>
                                <div class="vital-label">Weight</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="vital-sign">
                                <div class="vital-value">{{ $consultation->o2 }}%</div>
                                <div class="vital-label">Oxygen Saturation</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="vital-sign">
                                <div class="vital-value">{{ $consultation->pr }} bpm</div>
                                <div class="vital-label">Pulse Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clinical Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-clipboard-pulse me-2"></i>Clinical Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="clinical-section mb-4">
                        <h6 class="text-primary mb-2">Chief Complaint</h6>
                        <div class="clinical-content">
                            {{ $consultation->chief_complaint }}
                        </div>
                    </div>


                </div>
            </div>



            <!-- Additional Notes Card -->
            @if($consultation->consultation_notes)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-journal-text me-2"></i>Additional Notes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="clinical-content">
                            {{ $consultation->consultation_notes }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Quick Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil me-2"></i>Edit Consultation
                                </a>
                                <a href="{{ route('consultations.create') }}?patient_id={{ $consultation->patient->patient_id }}" class="btn btn-success">
                                    <i class="bi bi-file-earmark-plus me-2"></i>New Consultation
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Navigation</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('patients.consultations', $consultation->patient) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-clock-history me-2"></i>Patient History
                                </a>
                                <a href="{{ route('patients.lab-results', $consultation->patient) }}" class="btn btn-outline-info">
                                    <i class="bi bi-clipboard-data me-2"></i>Lab Results
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem 0;
}

.info-item strong {
    font-weight: 600;
    color: #495057;
    min-width: 100px;
}

.vital-sign {
    padding: 1rem 0;
    border-radius: 0.5rem;
    background: #f8f9fa;
    margin-bottom: 0.5rem;
}

.vital-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #0d6efd;
}

.vital-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.clinical-section {
    border-left: 4px solid #0d6efd;
    padding-left: 1rem;
}

.clinical-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    font-size: 1rem;
    line-height: 1.6;
    color: #495057;
    white-space: pre-wrap;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.patient-avatar {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .vital-sign {
        margin-bottom: 1rem;
    }
    
    .vital-value {
        font-size: 1.25rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .info-item strong {
        min-width: auto;
    }
}

/* Print styles */
@media print {
    .btn, .card-header {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        break-inside: avoid;
        margin-bottom: 1rem !important;
    }
    
    .clinical-content {
        background: white !important;
        border: 1px solid #ddd;
    }
}
</style>
@endsection
