@extends('layouts.app')

@section('title', 'Lab Results - ' . $patient->full_name)

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0 text-gray-800">Lab Results History</h1>
        <small class="text-muted">{{ $patient->full_name }} (ID: {{ $patient->patient_id }})</small>
    </div>
    <div>
        <a href="{{ route('lab-records.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-info me-2">
            <i class="bi bi-upload me-1"></i>Upload Lab Result
        </a>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Patient
        </a>
    </div>
</div>

<!-- Patient Summary Card -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle me-3 p-2">
                        <i class="bi bi-person text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $patient->full_name }}</h6>
                        <small class="text-muted">{{ $patient->age }} years, {{ $patient->sex }} | {{ $patient->contact_number }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-info">{{ $labResults->count() }} Lab Result(s)</span>
            </div>
        </div>
    </div>
</div>

<!-- Lab Results Filter -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('patients.lab-results', $patient) }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="X-ray" {{ request('type') == 'X-ray' ? 'selected' : '' }}>X-ray</option>
                        <option value="ECG" {{ request('type') == 'ECG' ? 'selected' : '' }}>ECG</option>
                        <option value="Ultrasound" {{ request('type') == 'Ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                        <option value="Lab Test" {{ request('type') == 'Lab Test' ? 'selected' : '' }}>Lab Test</option>
                        <option value="Medical" {{ request('type') == 'Medical' ? 'selected' : '' }}>Medical</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lab Results List -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-card-image me-2"></i>Lab Results
            @if(request('type'))
                - {{ request('type') }}
            @endif
        </h6>
    </div>
    <div class="card-body p-0">
        @if($labResults->count() > 0)
            <div class="lab-results-grid">
                @foreach($labResults as $labResult)
                    <div class="lab-result-item border-bottom p-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="lab-type-badge text-center">
                                    @switch($labResult->type)
                                        @case('X-ray')
                                            <div class="badge bg-primary p-2 w-100">
                                                <i class="bi bi-radioactive display-6"></i>
                                                <div class="mt-1">X-ray</div>
                                            </div>
                                            @break
                                        @case('ECG')
                                            <div class="badge bg-success p-2 w-100">
                                                <i class="bi bi-heart-pulse display-6"></i>
                                                <div class="mt-1">ECG</div>
                                            </div>
                                            @break
                                        @case('Ultrasound')
                                            <div class="badge bg-info p-2 w-100">
                                                <i class="bi bi-soundwave display-6"></i>
                                                <div class="mt-1">Ultrasound</div>
                                            </div>
                                            @break
                                        @case('Lab Test')
                                            <div class="badge bg-warning p-2 w-100">
                                                <i class="bi bi-droplet display-6"></i>
                                                <div class="mt-1">Lab Test</div>
                                            </div>
                                            @break
                                        @default
                                            <div class="badge bg-secondary p-2 w-100">
                                                <i class="bi bi-file-medical display-6"></i>
                                                <div class="mt-1">{{ $labResult->type }}</div>
                                            </div>
                                    @endswitch
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="lab-details">
                                    <h6 class="mb-1">{{ $labResult->type }}</h6>
                                    <p class="text-muted mb-2">
                                        @if($labResult->test_date)
                                            {{ $labResult->test_date->format('F d, Y - g:i A') }}
                                        @else
                                            No date/time recorded
                                        @endif
                                    </p>
                                    
                                    @if($labResult->consultation)
                                        <div class="consultation-link">
                                            <small class="text-primary">
                                                <i class="bi bi-link me-1"></i>
                                                Related to consultation: 
                                                @if($labResult->consultation && $labResult->consultation->consultation_date)
                                                    {{ $labResult->consultation->consultation_date->format('M d, Y') }}
                                                @else
                                                    No consultation date
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="lab-status text-center">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Available
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="lab-actions text-center">
                                    <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-sm btn-outline-info w-100 mb-1">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    @if($labResult->consultation)
                                        <a href="{{ route('consultations.show', $labResult->consultation) }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="bi bi-file-earmark-medical me-1"></i>Consultation
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-card-image display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No lab results found</h5>
                @if(request()->hasAny(['type', 'date_from', 'date_to']))
                    <p class="text-muted">Try adjusting your filter criteria.</p>
                    <a href="{{ route('patients.lab-results', $patient) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                    </a>
                @else
                    <p class="text-muted">This patient doesn't have any lab results yet.</p>
                    <a href="{{ route('lab-records.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-info">
                        <i class="bi bi-upload me-2"></i>Upload First Lab Result
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@if($labResults->count() > 0)
    <!-- Lab Results Summary -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Lab Results Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @php
                            $typeCounts = $labResults->groupBy('type')->map->count();
                        @endphp
                        @foreach($typeCounts as $type => $count)
                            <div class="col-md-2 mb-2">
                                <div class="summary-item">
                                    <h4 class="text-primary">{{ $count }}</h4>
                                    <small class="text-muted">{{ $type }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
