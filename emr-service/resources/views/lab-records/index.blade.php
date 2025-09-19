@extends('layouts.app')

@section('title', 'Lab Records')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Lab Records Management</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - View Lab Records</small>
        </div>
        <div class="d-flex gap-2">
            <!-- View-only interface for clinic staff -->
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
        <div>
                    <strong>View Mode:</strong> Search and view lab records for patient assistance
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <small>Total Records</small>
                        </div>
                        <i class="bi bi-journal-medical display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                            <small>Pending Results</small>
                        </div>
                        <i class="bi bi-clock-history display-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                            <small>Completed Results</small>
                        </div>
                        <i class="bi bi-check2-circle display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Search & Filter Lab Records
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lab-records.index') }}">
                <div class="row g-3">
                    <!-- Primary Search - "Just type the name and see everything" -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 1.1em;">
                            <i class="bi bi-search me-1"></i>Search Everything
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-lg" 
                               value="{{ request('search') }}"
                               placeholder="Type patient name, test name, or any keyword..."
                               style="font-size: 1.1em;">
                        <small class="text-muted">Search by patient name, test type, test name, or results</small>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="ready_for_review" {{ request('status') == 'ready_for_review' ? 'selected' : '' }}>Ready for Review</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Test Type</label>
                        <select name="type" class="form-select form-select-lg">
                            <option value="">All Types</option>
                            <option value="Laboratory" {{ request('type') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="X-ray" {{ request('type') == 'X-ray' ? 'selected' : '' }}>X-ray</option>
                            <option value="ECG" {{ request('type') == 'ECG' ? 'selected' : '' }}>ECG</option>
                            <option value="Ultrasound" {{ request('type') == 'Ultrasound' ? 'selected' : '' }}>Ultrasound</option>
                            <option value="CT Scan" {{ request('type') == 'CT Scan' ? 'selected' : '' }}>CT Scan</option>
                            <option value="MRI" {{ request('type') == 'MRI' ? 'selected' : '' }}>MRI</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Priority</label>
                        <select name="priority" class="form-select form-select-lg">
                            <option value="">All Priorities</option>
                            <option value="stat" {{ request('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="routine" {{ request('priority') == 'routine' ? 'selected' : '' }}>Routine</option>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-lg" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-lg" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg flex-fill">
                            <i class="bi bi-search me-2"></i>Search Records
                        </button>
                        <a href="{{ route('lab-records.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lab Records List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Lab Records 
                @if(request()->hasAny(['search', 'status', 'type', 'priority', 'date_from', 'date_to']))
                    <small class="text-muted">
                        ({{ $labResults->total() }} results found)
                    </small>
                @endif
            </h6>
            @if(request()->hasAny(['search', 'status', 'type', 'priority', 'date_from', 'date_to']))
                <small class="text-info">
                    <i class="bi bi-funnel-fill me-1"></i>Filtered Results
                </small>
            @endif
        </div>
        <div class="card-body p-0">
            @if($labResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 1.05em;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 15%;">Patient</th>
                                <th style="width: 15%;">Test Info</th>
                                <th style="width: 15%;">Date & Time</th>
                                <th style="width: 10%;">Priority</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 15%;">Staff</th>
                                <th style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labResults as $labResult)
                                <tr>
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
                                    </td>
                                    <td>
                                        @switch($labResult->priority)
                                            @case('stat')
                                                <span class="badge bg-danger fs-6">STAT</span>
                                                @break
                                            @case('urgent')
                                                <span class="badge bg-warning fs-6">URGENT</span>
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
                                        <div class="small">
                                            @if($labResult->requested_by)
                                                <div><strong>Req:</strong> {{ $labResult->requested_by }}</div>
                                            @endif
                                            @if($labResult->performed_by)
                                                <div><strong>Tech:</strong> {{ $labResult->performed_by }}</div>
                                            @endif
                                            @if($labResult->reviewed_by)
                                                <div><strong>Rev:</strong> {{ $labResult->reviewed_by }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm gap-1">
                                            <!-- View only for clinic staff -->
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
                    {{ $labResults->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-file-medical display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">
                        @if(request()->hasAny(['search', 'status', 'type', 'priority', 'date_from', 'date_to']))
                            No lab records match your search criteria
                        @else
                            No lab records found
                        @endif
                    </h5>
                    @if(request()->hasAny(['search', 'status', 'type', 'priority', 'date_from', 'date_to']))
                        <p class="text-muted">Try adjusting your search or filter criteria to find lab records.</p>
                        <a href="{{ route('lab-records.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear All Filters
                        </a>
                    @else
                        <p class="text-muted">No lab records are available to view at this time.</p>
                        <p class="text-muted small">Lab records will appear here once they are created by medical staff.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Doctor Review Modals -->
@foreach($labResults as $labResult)
    @if($labResult->status === 'ready_for_review')
        <div class="modal fade" id="reviewModal{{ $labResult->result_id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle me-2"></i>Review Lab Result
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
                                    </span>
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
                            
                            <div class="mb-3">
                                <label for="reviewed_by" class="form-label fw-bold">Reviewed By: <span class="text-danger">*</span></label>
                                <input type="text" name="reviewed_by" class="form-control" required placeholder="Enter doctor name">
                            </div>
                            
                            <div class="mb-3">
                                <label for="result_interpretation" class="form-label fw-bold">Result Interpretation:</label>
                                <textarea name="result_interpretation" class="form-control" rows="3" 
                                          placeholder="Enter your interpretation of the results...">{{ $labResult->result_notes }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="doctor_notes" class="form-label fw-bold">Doctor Notes: <span class="text-danger">*</span></label>
                                <textarea name="doctor_notes" class="form-control" rows="4" required
                                          placeholder="Enter your medical interpretation and recommendations..."></textarea>
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
    @endif
@endforeach

<!-- Enhanced Senior-Friendly Styles -->
<style>
.form-control-lg, .form-select-lg {
    font-size: 1.1em !important;
    padding: 0.75rem 1rem !important;
    border-radius: 0.5rem !important;
    border: 2px solid #dee2e6 !important;
    transition: all 0.3s ease;
    background-color: #fff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control-lg:focus, .form-select-lg:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.3) !important;
    transform: scale(1.02);
}

.btn-lg {
    font-size: 1.1em;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
}

.table th, .table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.badge.fs-6 {
    font-size: 0.9rem !important;
    padding: 0.5em 0.75em;
}

.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

.btn-group-vertical .btn {
    border-radius: 0.375rem !important;
}
</style>
@endsection
