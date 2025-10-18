@extends('layouts.doctor')

@section('title', 'Patient Details - ' . $patient->full_name)

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0 text-gray-800">Patient Details</h1>
        <small class="text-muted">Patient ID: {{ $patient->patient_code }}</small>
    </div>
    <div>
        <a href="{{ route('doctor.patient-records') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Patient Records
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
                    <span class="badge bg-primary">ID: {{ $patient->patient_code }}</span>
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
                    <a href="{{ route('doctor.consultation', $patient) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-medical me-2"></i>Start Consultation
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
                    <i class="bi bi-clipboard2-pulse me-2"></i>Recent Consultations
                </h6>
            </div>
            <div class="card-body">
                @if($consultations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Chief Complaint</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consultations->take(10) as $consultation)
                                <tr>
                                    <td>
                                        <div><strong>{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('M d, Y') }}</strong></div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('g:i A') }}</small>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($consultation->chief_complaint ?? 'N/A', 40) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($consultation->assessment ?? 'N/A', 40) }}</td>
                                    <td>
                                        @if($consultation->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($consultation->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($consultation->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-x display-4"></i>
                        <p class="mt-2">No consultation records yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Lab Results -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-clipboard-data me-2"></i>Recent Lab Results
                </h6>
            </div>
            <div class="card-body">
                @if($labResults->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Test Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($labResults->take(10) as $labResult)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($labResult->test_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($labResult->type === 'Laboratory')
                                            <span class="badge bg-info">Lab</span>
                                        @elseif($labResult->type === 'X-ray')
                                            <span class="badge bg-primary">X-ray</span>
                                        @else
                                            <span class="badge bg-success">{{ $labResult->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $labResult->test_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($labResult->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($labResult->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($labResult->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-clipboard-x display-4"></i>
                        <p class="mt-2">No lab results yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

