@extends('layouts.app')

@section('title', 'Pending Lab Reviews')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">
                <i class="bi bi-eye me-2"></i>Pending Lab Reviews
                <span class="badge bg-warning ms-2">{{ $pendingResults->total() }} pending</span>
            </h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Doctor Review Dashboard</small>
        </div>
        <div>
            <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to All Records
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $pendingResults->total() }}</div>
                            <div class="small">Awaiting Review</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $pendingResults->where('priority', 'stat')->count() }}</div>
                            <div class="small">STAT Priority</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="h3 mb-0">{{ $pendingResults->where('priority', 'urgent')->count() }}</div>
                            <div class="small">Urgent Priority</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-lightning display-6"></i>
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
                            <div class="h3 mb-0">{{ $pendingResults->where('created_at', '>=', now()->startOfDay())->count() }}</div>
                            <div class="small">Today's Results</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-day display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reviews List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-list-check me-2"></i>Lab Results Ready for Review
            </h6>
        </div>
        <div class="card-body p-0">
            @if($pendingResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 1.05em;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 20%;">Patient</th>
                                <th style="width: 20%;">Test Details</th>
                                <th style="width: 15%;">Date & Time</th>
                                <th style="width: 10%;">Priority</th>
                                <th style="width: 15%;">Technician</th>
                                <th style="width: 20%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingResults as $labResult)
                                <tr class="{{ $labResult->priority === 'stat' ? 'table-danger' : ($labResult->priority === 'urgent' ? 'table-warning' : '') }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
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
                                            <br><small class="text-success">Completed: {{ $labResult->completed_at->format('g:i A') }}</small>
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
                                        <div class="small">
                                            @if($labResult->performed_by)
                                                <div><strong>Tech:</strong> {{ $labResult->performed_by }}</div>
                                            @endif
                                            @if($labResult->technician_notes)
                                                <div class="text-muted mt-1">
                                                    <i class="bi bi-chat-text me-1"></i>Notes available
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#reviewModal{{ $labResult->result_id }}">
                                                <i class="bi bi-check-circle me-1"></i>Review Now
                                            </button>
                                            <a href="{{ route('lab-records.show', $labResult) }}" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye me-1"></i>Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-light">
                    {{ $pendingResults->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-check-all display-1 text-success"></i>
                    <h5 class="mt-3 text-success">All caught up!</h5>
                    <p class="text-muted">There are no lab results pending review at this moment.</p>
                    <a href="{{ route('lab-records.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back to All Records
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Doctor Review Modals -->
@foreach($pendingResults as $labResult)
    <div class="modal fade" id="reviewModal{{ $labResult->result_id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Review Lab Result
                        @if($labResult->priority === 'stat')
                            <span class="badge bg-danger ms-2">STAT</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('lab-records.mark-reviewed', $labResult) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Patient:</strong> {{ $labResult->patient->full_name }}<br>
                                <strong>Test:</strong> {{ $labResult->type }} - {{ $labResult->test_name }}<br>
                                <strong>Date:</strong> 
                                @if($labResult->test_date)
                                    {{ $labResult->test_date->format('M d, Y g:i A') }}
                                @else
                                    Not specified
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Performed by:</strong> {{ $labResult->performed_by ?? 'N/A' }}<br>
                                <strong>Priority:</strong> 
                                <span class="badge bg-{{ $labResult->priority === 'stat' ? 'danger' : ($labResult->priority === 'urgent' ? 'warning' : 'secondary') }}">
                                    {{ strtoupper($labResult->priority) }}
                                </span><br>
                                <strong>Completed:</strong> {{ $labResult->completed_at ? $labResult->completed_at->format('M d, Y g:i A') : 'N/A' }}
                            </div>
                        </div>
                        
                        @if($labResult->technician_notes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Technician Notes:</label>
                                <div class="border p-2 bg-light rounded">{{ $labResult->technician_notes }}</div>
                            </div>
                        @endif
                        
                        @if($labResult->result_value)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Result Value:</label>
                                <div class="border p-2 bg-light rounded">{{ $labResult->result_value }}</div>
                            </div>
                        @endif
                        
                        @if($labResult->reference_range)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reference Range:</label>
                                <div class="border p-2 bg-light rounded">{{ $labResult->reference_range }}</div>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="result_interpretation" class="form-label fw-bold">Result Interpretation:</label>
                            <textarea name="result_interpretation" class="form-control" rows="3" 
                                      placeholder="Enter your interpretation of the results...">{{ $labResult->result_notes }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="doctor_notes" class="form-label fw-bold">Doctor Notes: <span class="text-danger">*</span></label>
                            <textarea name="doctor_notes" class="form-control" rows="4" required
                                      placeholder="Enter your medical interpretation, diagnosis, and recommendations..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Mark as Reviewed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

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
</style>
@endsection
