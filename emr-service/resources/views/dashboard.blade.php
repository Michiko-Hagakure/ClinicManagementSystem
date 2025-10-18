@extends('layouts.app')

@section('title', 'EMR Dashboard')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 text-gray-800">EMR Dashboard</h1>
    <div class="text-muted">
        <i class="bi bi-clock me-1"></i>{{ now()->format('M d, Y - H:i') }}
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row g-2 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="card border-left-primary shadow-sm h-100">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Patients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPatients ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
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
                            Today's Consultations</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayConsultations ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-file-earmark-medical text-success" style="font-size: 2rem;"></i>
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
                            Total Lab Results</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingLabResults ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-card-image text-info" style="font-size: 2rem;"></i>
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
                            This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyConsultations ?? 0 }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-calendar3 text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2 bg-light">
        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
    </div>
    <div class="card-body py-3">
        <div class="row g-2">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('patients.create') }}" class="btn btn-primary w-100 btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Add Patient
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('consultations.create') }}" class="btn btn-success w-100 btn-sm">
                    <i class="bi bi-file-earmark-plus me-1"></i>New Consultation
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('lab-records.create') }}" class="btn btn-info w-100 btn-sm">
                    <i class="bi bi-upload me-1"></i>Upload Lab
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('patients.index') }}" class="btn btn-outline-primary w-100 btn-sm">
                    <i class="bi bi-search me-1"></i>Search Patients
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="bi bi-list me-1"></i>All Consultations
                </a>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="{{ route('lab-records.index') }}" class="btn btn-outline-info w-100 btn-sm">
                    <i class="bi bi-card-list me-1"></i>Lab Records
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-2">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 bg-light">
                <h6 class="m-0 font-weight-bold text-primary">Recent Patients</h6>
            </div>
            <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                @if(isset($recentPatients) && count($recentPatients) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentPatients as $patient)
                            <a href="{{ route('patients.show', $patient) }}" class="list-group-item list-group-item-action py-2 border-0">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $patient->first_name }} {{ $patient->last_name }}</h6>
                                        <small class="text-muted">{{ $patient->age ?? 'N/A' }} years, {{ ucfirst($patient->gender) }} | {{ $patient->phone_number ?? 'N/A' }}</small>
                                    </div>
                                    <small class="text-muted">ID: P{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-people display-4"></i>
                        <p class="mt-2">No recent patients found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 bg-light">
                <h6 class="m-0 font-weight-bold text-primary">Recent Consultations</h6>
            </div>
            <div class="card-body p-2" style="max-height: 400px; overflow-y: auto;">
                @if(isset($recentConsultations) && count($recentConsultations) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentConsultations as $consultation)
                            <a href="{{ route('consultations.show', $consultation) }}" class="list-group-item list-group-item-action py-2 border-0">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h6>
                                        <p class="mb-1 small">{{ Str::limit($consultation->chief_complaint ?? 'No complaint', 60) }}</p>
                                        <small class="text-muted">BP: {{ $consultation->bp ?? '0/0' }} | Temp: {{ $consultation->temparature ?? '0.0' }}°C</small>
                                    </div>
                                    <small class="text-muted ms-2">
                                        {{ $consultation->created_at->format('M d') }}
                                    </small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-file-earmark-medical display-4"></i>
                        <p class="mt-2">No recent consultations found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
