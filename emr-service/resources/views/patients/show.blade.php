@extends('layouts.app')

@section('title', 'Patient Details - ' . $patient->full_name)

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0 text-gray-800">Patient Details</h1>
        <small class="text-muted">Patient ID: {{ $patient->patient_id }}</small>
    </div>
    <div>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning me-2">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Patient Information Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person-circle me-2"></i>Patient Information
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-2">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-person display-6 text-muted"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $patient->full_name }}</h5>
                    <span class="badge bg-primary">ID: {{ $patient->patient_id }}</span>
                </div>
                
                <div class="patient-info">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Age:</div>
                        <div class="col-7"><strong>{{ $patient->age }} years old</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Sex:</div>
                        <div class="col-7">
                            <span class="badge bg-info">{{ $patient->gender ? ucfirst($patient->gender) : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Birth Date:</div>
                        <div class="col-7">
                            @if ($patient->date_of_birth)
                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('F d, Y') }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Civil Status:</div>
                        <div class="col-7">{{ $patient->civil_status ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Contact:</div>
                        <div class="col-7">
                            <i class="bi bi-telephone me-1"></i>{{ $patient->phone_number ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Address:</div>
                        <div class="col-7">{{ $patient->address }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('consultations.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-medical me-2"></i>New Consultation
                    </a>
                    <a href="{{ route('lab-records.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-info">
                        <i class="bi bi-upload me-2"></i>Upload Lab Result
                    </a>
                    <a href="{{ route('patients.consultations', $patient) }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View All Consultations
                    </a>
                    <a href="{{ route('patients.lab-results', $patient) }}" class="btn btn-outline-info">
                        <i class="bi bi-card-list me-2"></i>View Lab Results
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Medical History -->
    <div class="col-lg-8">
        <!-- Recent Consultations -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-file-earmark-medical me-2"></i>Recent Consultations
                </h6>
                <a href="{{ route('patients.consultations', $patient) }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($patient->consultations && $patient->consultations->count() > 0)
                    <div class="consultation-list">
                        @foreach($patient->consultations->take(3) as $consultation)
                            <div class="consultation-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            @if($consultation->consultation_date)
                                                {{ $consultation->consultation_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No date</span>
                                            @endif
                                        </h6>
                                        <p class="mb-2 text-muted">
                                            <strong>Chief Complaint:</strong> {{ Str::limit($consultation->chief_complaint, 80) }}
                                        </p>
                                        <div class="row text-sm">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-heart-pulse me-1"></i>BP: {{ $consultation->bp }}
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-thermometer me-1"></i>Temp: {{ $consultation->temparature }}°C
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-medical display-4 text-muted"></i>
                        <h6 class="mt-2 text-muted">No consultations yet</h6>
                        <p class="text-muted">Start by creating the first consultation for this patient.</p>
                        <a href="{{ route('consultations.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Create First Consultation
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Lab Results -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-card-image me-2"></i>Recent Lab Results
                </h6>
                <a href="{{ route('patients.lab-results', $patient) }}" class="btn btn-sm btn-outline-info">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($patient->labResults && $patient->labResults->count() > 0)
                    <div class="lab-results-list">
                        @foreach($patient->labResults->take(5) as $labResult)
                            <div class="lab-result-item d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <h6 class="mb-0">{{ $labResult->type }}</h6>
                                    <small class="text-muted">
                                        @if($labResult->test_date)
                                            {{ $labResult->test_date->format('M d, Y - H:i') }}
                                        @else
                                            No date/time recorded
                                        @endif
                                    </small>
                                </div>
                                <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-sm btn-outline-info">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-card-image display-4 text-muted"></i>
                        <h6 class="mt-2 text-muted">No lab results yet</h6>
                        <p class="text-muted">Upload lab results for this patient.</p>
                        <a href="{{ route('lab-records.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-info">
                            <i class="bi bi-upload me-2"></i>Upload First Lab Result
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
