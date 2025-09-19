@extends('layouts.doctor')

@section('title', 'Lab Results Review - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Lab Results Review</h1>
        <p class="text-muted mb-0">Review and approve pending laboratory results</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-left-warning shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingResults->total() }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-clipboard-pulse text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-info shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Today's Results</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pendingResults->filter(function($result) { return $result->test_date->isToday(); })->count() }}
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-calendar-check text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-danger shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Urgent Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pendingResults->filter(function($result) { return $result->test_date->diffInDays(now()) > 2; })->count() }}
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lab Results List -->
<div class="card shadow">
    <div class="card-header bg-info text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-list-ul me-2"></i>Pending Lab Results
        </h6>
    </div>
    <div class="card-body p-0">
        @if($pendingResults->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Patient</th>
                            <th class="py-3">Test Type</th>
                            <th class="py-3">Test Date</th>
                            <th class="py-3">Urgency</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingResults as $labResult)
                        <tr>
                            <td class="py-3">
                                <div>
                                    <h6 class="mb-1">{{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person-badge me-1"></i>{{ $labResult->patient->patient_id }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-gender-{{ strtolower($labResult->patient->sex ?? 'ambiguous') }} me-1"></i>{{ $labResult->patient->sex ?? 'N/A' }}
                                    </small>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold">{{ $labResult->test_type }}</span>
                                @if($labResult->test_category)
                                    <br><small class="text-muted">{{ $labResult->test_category }}</small>
                                @endif
                            </td>
                            <td class="py-3">
                                <div>
                                    <span class="fw-bold">{{ $labResult->test_date->format('M d, Y') }}</span>
                                    <br><small class="text-muted">{{ $labResult->test_date->format('H:i') }}</small>
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    $daysSince = $labResult->test_date->diffInDays(now());
                                @endphp
                                @if($daysSince > 2)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Urgent
                                    </span>
                                @elseif($daysSince > 1)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Priority
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-check-circle me-1"></i>Normal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending Review
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $labResult->id }}">
                                        <i class="bi bi-eye me-1"></i>Review
                                    </button>
                                    <a href="{{ route('patients.show', $labResult->patient) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-person me-1"></i>Patient
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Review Modal -->
                        <div class="modal fade" id="reviewModal{{ $labResult->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-clipboard-pulse me-2"></i>Review Lab Result
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Patient Info -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Patient Information</h6>
                                                <p class="mb-1"><strong>Name:</strong> {{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</p>
                                                <p class="mb-1"><strong>ID:</strong> {{ $labResult->patient->patient_id }}</p>
                                                <p class="mb-0"><strong>Age/Sex:</strong> 
                                                    @if($labResult->patient->date_of_birth)
                                                        {{ $labResult->patient->date_of_birth->age }} years
                                                    @else
                                                        N/A
                                                    @endif
                                                    / {{ $labResult->patient->sex ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Test Information</h6>
                                                <p class="mb-1"><strong>Test Type:</strong> {{ $labResult->test_type }}</p>
                                                <p class="mb-1"><strong>Date:</strong> {{ $labResult->test_date->format('M d, Y H:i') }}</p>
                                                <p class="mb-0"><strong>Category:</strong> {{ $labResult->test_category ?? 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <!-- Test Results -->
                                        <div class="mb-3">
                                            <h6 class="text-primary">Test Results</h6>
                                            @if($labResult->results)
                                                <div class="bg-light p-3 rounded">
                                                    <pre>{{ $labResult->results }}</pre>
                                                </div>
                                            @else
                                                <p class="text-muted">No results data available</p>
                                            @endif
                                        </div>

                                        <!-- Review Form -->
                                        <form action="{{ route('doctor.lab-results.review', $labResult) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="mb-3">
                                                <label for="doctor_notes{{ $labResult->id }}" class="form-label">
                                                    <strong>Doctor's Notes and Interpretation</strong>
                                                </label>
                                                <textarea class="form-control" id="doctor_notes{{ $labResult->id }}" name="doctor_notes" rows="4" 
                                                          placeholder="Enter your clinical interpretation, recommendations, and any follow-up needed...">{{ $labResult->doctor_notes }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="status{{ $labResult->id }}" class="form-label">
                                                    <strong>Review Status</strong>
                                                </label>
                                                <select class="form-select" id="status{{ $labResult->id }}" name="status" required>
                                                    <option value="reviewed" {{ $labResult->status === 'reviewed' ? 'selected' : '' }}>
                                                        Reviewed - Normal
                                                    </option>
                                                    <option value="requires_followup" {{ $labResult->status === 'requires_followup' ? 'selected' : '' }}>
                                                        Reviewed - Requires Follow-up
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle me-1"></i>Complete Review
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pendingResults->hasPages())
                <div class="card-footer">
                    {{ $pendingResults->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">All Lab Results Reviewed</h4>
                <p class="text-muted">Great job! You're all caught up with lab result reviews.</p>
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2 me-1"></i>Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
