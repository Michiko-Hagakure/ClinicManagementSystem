@extends('layouts.doctor')

@section('title', 'Patient Queue - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Patient Queue</h1>
        <p class="text-muted mb-0">Patients waiting for consultation today</p>
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

<!-- Queue Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-left-warning shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Patients Waiting</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $waitingPatients->total() }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
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
                            Average Wait Time</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($waitingPatients->count() > 0)
                                {{ $waitingPatients->first()->created_at->diffForHumans(null, true) }}
                            @else
                                0 min
                            @endif
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-stopwatch text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-success shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Next Patient</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            @if($waitingPatients->count() > 0)
                                {{ $waitingPatients->first()->patient->first_name }} {{ $waitingPatients->first()->patient->last_name }}
                            @else
                                None waiting
                            @endif
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Queue List -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-list-ul me-2"></i>Waiting Patients
        </h6>
    </div>
    <div class="card-body p-0">
        @if($waitingPatients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">Patient Information</th>
                            <th class="py-3">Wait Time</th>
                            <th class="py-3">Payment Status</th>
                            <th class="py-3">Priority</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waitingPatients as $index => $consultation)
                        <tr class="{{ $index === 0 ? 'table-warning' : '' }}">
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    @if($index === 0)
                                        <span class="badge bg-success me-2">NEXT</span>
                                    @endif
                                    <strong>{{ $index + 1 }}</strong>
                                </div>
                            </td>
                            <td class="py-3">
                                <div>
                                    <h6 class="mb-1">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person-badge me-1"></i>ID: {{ $consultation->patient->patient_id }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-calendar me-1"></i>{{ $consultation->patient->date_of_birth ? $consultation->patient->date_of_birth->format('M d, Y') : 'N/A' }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-gender-{{ strtolower($consultation->patient->sex ?? 'ambiguous') }} me-1"></i>{{ $consultation->patient->sex ?? 'N/A' }}
                                    </small>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $consultation->created_at->diffForHumans(null, true) }}</span>
                                    <small class="text-muted">Since {{ $consultation->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Paid
                                </span>
                            </td>
                            <td class="py-3">
                                @php
                                    $waitMinutes = $consultation->created_at->diffInMinutes(now());
                                @endphp
                                @if($waitMinutes > 60)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>High
                                    </span>
                                @elseif($waitMinutes > 30)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-dash-circle me-1"></i>Medium
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-check-circle me-1"></i>Normal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('doctor.consultation', $consultation->patient) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-person-check me-1"></i>See Patient
                                    </a>
                                    <a href="{{ route('patients.show', $consultation->patient) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-eye me-1"></i>History
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($waitingPatients->hasPages())
                <div class="card-footer">
                    {{ $waitingPatients->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No Patients Waiting</h4>
                <p class="text-muted">Great job! You're all caught up with today's consultations.</p>
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2 me-1"></i>Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
@if($waitingPatients->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="card-title text-success">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('doctor.consultation', $waitingPatients->first()->patient) }}" 
                       class="btn btn-success">
                        <i class="bi bi-person-check me-1"></i>See Next Patient
                    </a>
                    <a href="{{ route('doctor.lab-results') }}" class="btn btn-outline-info">
                        <i class="bi bi-clipboard-pulse me-1"></i>Review Lab Results
                    </a>
                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Queue
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
// Auto-refresh every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endsection
