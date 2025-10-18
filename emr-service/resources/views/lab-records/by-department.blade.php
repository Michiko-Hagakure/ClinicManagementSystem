@extends('layouts.app')

@section('title', ucfirst($department) . ' Department - Lab Records')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">
                <i class="bi bi-diagram-3 me-2"></i>{{ ucfirst($department) }} Department
                <span class="badge bg-info ms-2">{{ $labResults->total() }} records</span>
            </h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - {{ ucfirst($department) }} Lab Records</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lab-records.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>New Test
            </a>
            <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>All Records
            </a>
        </div>
    </div>

    <!-- Department Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $labResults->total() }}</div>
                            <div class="small">Total {{ ucfirst($department) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-{{ $department === 'laboratory' ? 'flask' : ($department === 'radiology' ? 'x-ray' : 'heart-pulse') }} display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $labResults->where('status', 'pending')->count() }}</div>
                            <div class="small">Pending Tests</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $labResults->where('status', 'in_progress')->count() }}</div>
                            <div class="small">In Progress</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-arrow-repeat display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $labResults->where('status', 'completed')->count() + $labResults->where('status', 'ready_for_review')->count() }}</div>
                            <div class="small">Completed Today</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions for Medical Staff -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-lightning me-2"></i>Quick Actions
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('lab-records.create') }}?type={{ $department === 'laboratory' ? 'Laboratory' : ($department === 'radiology' ? 'X-ray' : 'Ultrasound') }}" 
                       class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-plus-circle fs-4 d-block mb-2"></i>
                        <span class="fw-bold">Add New {{ $department === 'laboratory' ? 'Lab Test' : ($department === 'radiology' ? 'X-ray' : 'Ultrasound') }}</span>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('lab-records.index') }}?status=pending&type={{ $department === 'laboratory' ? 'Laboratory' : ($department === 'radiology' ? 'X-ray' : 'Ultrasound') }}" 
                       class="btn btn-outline-warning w-100 py-3">
                        <i class="bi bi-clock fs-4 d-block mb-2"></i>
                        <span class="fw-bold">View Pending Tests</span>
                        <small class="d-block text-muted">{{ $labResults->where('status', 'pending')->count() }} pending</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('lab-records.index') }}?status=in_progress&type={{ $department === 'laboratory' ? 'Laboratory' : ($department === 'radiology' ? 'X-ray' : 'Ultrasound') }}" 
                       class="btn btn-outline-info w-100 py-3">
                        <i class="bi bi-arrow-repeat fs-4 d-block mb-2"></i>
                        <span class="fw-bold">Continue Work</span>
                        <small class="d-block text-muted">{{ $labResults->where('status', 'in_progress')->count() }} in progress</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Records List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>{{ ucfirst($department) }} Records
            </h6>
            <div class="d-flex gap-2">
                <!-- Department filter buttons -->
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('lab-records.by-department', 'laboratory') }}" 
                       class="btn btn-{{ $department === 'laboratory' ? 'primary' : 'outline-primary' }}">
                        <i class="bi bi-flask me-1"></i>Lab
                    </a>
                    <a href="{{ route('lab-records.by-department', 'radiology') }}" 
                       class="btn btn-{{ $department === 'radiology' ? 'primary' : 'outline-primary' }}">
                        <i class="bi bi-x-ray me-1"></i>Radiology
                    </a>
                    <a href="{{ route('lab-records.by-department', 'ultrasound') }}" 
                       class="btn btn-{{ $department === 'ultrasound' ? 'primary' : 'outline-primary' }}">
                        <i class="bi bi-heart-pulse me-1"></i>Ultrasound
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($labResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 1.05em;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 20%;">Patient</th>
                                <th style="width: 20%;">Test Details</th>
                                <th style="width: 15%;">Date & Time</th>
                                <th style="width: 10%;">Priority</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labResults as $labResult)
                                <tr class="{{ $labResult->priority === 'stat' ? 'table-danger' : ($labResult->priority === 'urgent' ? 'table-warning' : '') }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $labResult->patient->full_name }}</div>
                                                <small class="text-muted">ID: {{ $labResult->patient->patient_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold text-primary">{{ $labResult->type }}</div>
                                            <small class="text-muted">{{ $labResult->test_name }}</small>
                                            @if($labResult->test_description)
                                                <br><small class="text-muted">{{ Str::limit($labResult->test_description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            @if($labResult->test_date)
                                                {{ $labResult->test_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No date</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            @if($labResult->test_date)
                                                {{ $labResult->test_date->format('g:i A') }}
                                            @else
                                                No time
                                            @endif
                                        </small>
                                        @if($labResult->completed_at)
                                            <br><small class="text-success">Done: {{ $labResult->completed_at->format('g:i A') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($labResult->priority)
                                            @case('stat')
                                                <span class="badge bg-danger fs-6 pulse">STAT</span>
                                                @break
                                            @case('urgent')
                                                <span class="badge bg-warning text-dark fs-6">URGENT</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary fs-6">ROUTINE</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($labResult->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark fs-6">
                                                    <i class="bi bi-clock me-1"></i>Pending
                                                </span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-info fs-6">
                                                    <i class="bi bi-arrow-repeat me-1"></i>In Progress
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success fs-6">
                                                    <i class="bi bi-check-circle me-1"></i>Completed
                                                </span>
                                                @break
                                            @case('ready_for_review')
                                                <span class="badge bg-primary fs-6">
                                                    <i class="bi bi-eye me-1"></i>Ready for Review
                                                </span>
                                                @break
                                            @case('reviewed')
                                                <span class="badge bg-dark fs-6">
                                                    <i class="bi bi-check-all me-1"></i>Reviewed
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <!-- Staff can only view lab results -->
                                            <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                            
                                            @if($labResult->consultation)
                                                <a href="{{ route('consultations.show', $labResult->consultation) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-file-earmark-medical me-1"></i>Related Consultation
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-light">
                    {{ $labResults->appends(['dept' => $department])->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-{{ $department === 'laboratory' ? 'flask' : ($department === 'radiology' ? 'x-ray' : 'heart-pulse') }} display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">No {{ $department }} records found</h5>
                    <p class="text-muted">{{ ucfirst($department) }} tests will appear here when doctors upload them.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.pulse {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn.py-3 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}
</style>
@endsection
