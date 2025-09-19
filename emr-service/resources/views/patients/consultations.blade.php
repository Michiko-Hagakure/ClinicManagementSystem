@extends('layouts.app')

@section('title', 'Consultations - ' . $patient->full_name)

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0 text-gray-800">Consultation History</h1>
        <small class="text-muted">{{ $patient->full_name }} (ID: {{ $patient->patient_id }})</small>
    </div>
    <div>
        <a href="{{ route('consultations.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-success me-2">
            <i class="bi bi-file-earmark-plus me-1"></i>New Consultation
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
                <span class="badge bg-info">{{ $consultations->count() }} Consultation(s)</span>
            </div>
        </div>
    </div>
</div>

<!-- Consultations List -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-file-earmark-medical me-2"></i>All Consultations
        </h6>
    </div>
    <div class="card-body p-0">
        @if($consultations->count() > 0)
            <div class="consultation-timeline">
                @foreach($consultations as $consultation)
                    <div class="consultation-item border-bottom p-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="consultation-date">
                                    <h6 class="text-primary mb-1">
                                        @if($consultation->consultation_date)
                                            {{ $consultation->consultation_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No date</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">
                                        @if($consultation->consultation_date)
                                            {{ $consultation->consultation_date->format('l') }}
                                        @else
                                            No day specified
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="consultation-details">
                                    <h6 class="mb-2">Chief Complaint</h6>
                                    <p class="mb-2">{{ $consultation->chief_complaint }}</p>
                                    
                                    @if($consultation->consultation_notes)
                                        <h6 class="mb-1">Notes</h6>
                                        <p class="text-muted small">{{ Str::limit($consultation->consultation_notes, 150) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="vital-signs mb-2">
                                    <h6 class="mb-2">Vital Signs</h6>
                                    <div class="vital-grid">
                                        <small class="d-block"><strong>BP:</strong> {{ $consultation->bp }}</small>
                                        <small class="d-block"><strong>Temp:</strong> {{ $consultation->temparature }}°C</small>
                                        <small class="d-block"><strong>Weight:</strong> {{ $consultation->weight }} kg</small>
                                        <small class="d-block"><strong>O2:</strong> {{ $consultation->o2 }}%</small>
                                        <small class="d-block"><strong>PR:</strong> {{ $consultation->pr }} bpm</small>
                                    </div>
                                </div>
                                <div class="consultation-actions">
                                    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                    @if($consultation->labResults && $consultation->labResults->count() > 0)
                                        <div class="mt-1">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>{{ $consultation->labResults->count() }} Lab Result(s)
                                            </small>
                                        </div>
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
                <i class="bi bi-file-earmark-medical display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No consultations found</h5>
                <p class="text-muted">This patient hasn't had any consultations yet.</p>
                <a href="{{ route('consultations.create') }}?patient_id={{ $patient->patient_id }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-plus me-2"></i>Create First Consultation
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
