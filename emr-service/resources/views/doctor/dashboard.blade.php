@extends('layouts.doctor')

@section('title', 'Doctor Dashboard - EMR System')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Doctor Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, Dr. Name! Here's your clinical overview for today.</p>
    </div>
    <div class="text-muted">
        <i class="bi bi-clock me-1"></i>{{ now()->format('M d, Y - H:i') }}
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-left-primary shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Today's Patients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayPatients ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-left-warning shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Consultations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingConsultations ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-left-success shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Completed Today</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedToday ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-left-info shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pending Lab Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingLabReviews ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-clipboard-pulse text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-3">
    <!-- Patient Queue -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-clock-history me-2"></i>Patient Queue
                </h6>
                <a href="{{ route('doctor.patient-queue') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                @if($waitingPatients && $waitingPatients->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($waitingPatients as $consultation)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h6>
                                <small class="text-muted">
                                    ID: {{ $consultation->patient->patient_id }} • 
                                    Waiting: {{ $consultation->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div>
                                <a href="{{ route('doctor.consultation', $consultation->patient) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-person-check me-1"></i>See Patient
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">No patients waiting</h6>
                        <small class="text-muted">Great job! You're all caught up.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lab Results Needing Review -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-clipboard-pulse me-2"></i>Lab Results to Review
                </h6>
                <a href="{{ route('doctor.lab-results') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                @if($pendingLabResults && $pendingLabResults->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingLabResults as $labResult)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">{{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</h6>
                                <small class="text-muted">
                                    {{ $labResult->test_type }} • {{ $labResult->test_date ? $labResult->test_date->format('M d, Y') : 'N/A' }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark">Pending Review</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">All results reviewed</h6>
                        <small class="text-muted">No pending lab results to review.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Consultations -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-file-earmark-medical me-2"></i>Recent Consultations
                </h6>
                <a href="{{ route('consultations.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                @if($recentConsultations && $recentConsultations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Chief Complaint</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentConsultations as $consultation)
                                <tr>
                                    <td>
                                        <strong>{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</strong>
                                        <br><small class="text-muted">{{ $consultation->patient->patient_id }}</small>
                                    </td>
                                    <td>{{ $consultation->consultation_date ? $consultation->consultation_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ Str::limit($consultation->chief_complaint ?? 'Not specified', 50) }}</td>
                                    <td>{{ Str::limit($consultation->diagnosis ?? 'Pending', 40) }}</td>
                                    <td>
                                        @if($consultation->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($consultation->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($consultation->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('doctor.consultation', $consultation->patient) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-medical text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">No recent consultations</h6>
                        <small class="text-muted">Consultations will appear here once you start seeing patients.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
