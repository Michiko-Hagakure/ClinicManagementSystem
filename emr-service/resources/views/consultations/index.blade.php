@php
    // Check if user is a doctor to use appropriate layout
    $userRole = session('user_role') ?? session('role', '');
    $layout = $userRole === 'doctor' ? 'layouts.doctor' : 'layouts.app';
@endphp

@extends($layout)

@section('title', 'All Consultations')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">All Consultations</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Consultation Records</small>
        </div>
        <div>
            <a href="{{ route('consultations.create') }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-plus me-1"></i>New Consultation
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-house me-1"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Consultations</h6>
                            <h3 class="mb-0">{{ $consultations->total() }}</h3>
                        </div>
                        <div class="bg-white rounded-circle p-2">
                            <i class="bi bi-file-earmark-medical text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Today's Consultations</h6>
                            <h3 class="mb-0">{{ $consultations->where('consultation_date', today())->count() }}</h3>
                        </div>
                        <div class="bg-white rounded-circle p-2">
                            <i class="bi bi-calendar-check text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Pending Follow-ups</h6>
                            <h3 class="mb-0">{{ $consultations->where('status', 'follow_up_required')->count() }}</h3>
                        </div>
                        <div class="bg-white rounded-circle p-2">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">This Month</h6>
                            <h3 class="mb-0">{{ $consultations->where('consultation_date', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <div class="bg-white rounded-circle p-2">
                            <i class="bi bi-graph-up text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('consultations.index') }}" id="filterForm">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" id="searchInput" class="form-control" 
                                   placeholder="Search consultations by patient name, complaint, or diagnosis..."
                                   value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                        <select name="status" id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="follow_up_required" {{ request('status') == 'follow_up_required' ? 'selected' : '' }}>Follow-up Required</option>
                    </select>
                </div>
                <div class="col-md-3">
                        <div class="input-group">
                            <input type="date" name="date" id="dateFilter" class="form-control"
                                   value="{{ request('date') }}">
                            @if(request()->hasAny(['search', 'status', 'date']))
                                <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                </div>
            </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-funnel me-1"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters Indicator -->
    @if(request()->hasAny(['search', 'status', 'date']))
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-funnel me-2"></i>
            <strong>Active Filters:</strong>
            @if(request('search'))
                <span class="badge bg-primary me-1">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('status'))
                <span class="badge bg-success me-1">Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}</span>
            @endif
            @if(request('date'))
                <span class="badge bg-warning me-1">Date: {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}</span>
            @endif
            <a href="{{ route('consultations.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="bi bi-x-circle me-1"></i>Clear All
            </a>
        </div>
    @endif

    <!-- Consultations List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-medical me-2"></i>Consultation Records
                @if($consultations->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $consultations->total() }} total</span>
                @endif
            </h6>
        </div>
        <div class="card-body p-0">
            @if($consultations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Chief Complaint</th>
                                <th>Assessment</th>
                                <th>Status</th>
                                <th>Follow-up</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultations as $consultation)
                                <tr>
                                    <td>
                                        <div class="consultation-date">
                                            @if ($consultation->consultation_date)
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('h:i A') }}</small>
                                            @else
                                                <span class="text-muted">No date</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            @if ($consultation->patient)
                                                <a href="{{ route('patients.show', $consultation->patient->id) }}" class="fw-bold text-decoration-none">{{ $consultation->patient->full_name }}</a>
                                                <div class="text-muted small">
                                                    ID: {{ $consultation->patient->patient_code }}
                                                </div>
                                            @else
                                                <span class="text-muted">Patient not found</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="chief-complaint">
                                            {{ Str::limit($consultation->chief_complaint, 60) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($consultation->assessment)
                                            <div class="assessment">
                                                {{ Str::limit($consultation->assessment, 50) }}
                                            </div>
                                        @else
                                            <small class="text-muted">No assessment recorded</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($consultation->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($consultation->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($consultation->status == 'follow_up_required')
                                            <span class="badge bg-info">Follow-up Required</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="follow-up-date text-muted">
                                            @if ($consultation->follow_up_date)
                                                Follow-up: <span class="fw-bold">{{ \Carbon\Carbon::parse($consultation->follow_up_date)->format('M d, Y') }}</span>
                                            @else
                                                No follow-up needed
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('consultations.show', $consultation) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('consultations.edit', $consultation) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $consultations->firstItem() }} to {{ $consultations->lastItem() }} of {{ $consultations->total() }} consultations
                            </small>
                        </div>
                        <div>
                            {{ $consultations->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-medical text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-muted">No Consultations Found</h5>
                    <p class="text-muted">Start by creating your first consultation record.</p>
                    <a href="{{ route('consultations.create') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-plus me-2"></i>Create First Consultation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced Filter JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const filterForm = document.getElementById('filterForm');
    const tableRows = document.querySelectorAll('tbody tr');

    let debounceTimer;

    // Auto-submit form with debounce for search input
    function debounceSubmit() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            filterForm.submit();
        }, 500);
    }

    // Immediate submit for select/date changes
    function immediateSubmit() {
        clearTimeout(debounceTimer);
        filterForm.submit();
    }

    // Real-time client-side filtering for better UX
    function filterTableClientSide() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const dateValue = dateFilter.value;

        let visibleCount = 0;

        tableRows.forEach(row => {
            const patientNameElement = row.querySelector('.patient-info a');
            const patientName = patientNameElement ? patientNameElement.textContent.toLowerCase() : '';
            const chiefComplaintElement = row.querySelector('.chief-complaint');
            const chiefComplaint = chiefComplaintElement ? chiefComplaintElement.textContent.toLowerCase() : '';
            const assessmentElement = row.querySelector('.assessment');
            const assessment = assessmentElement ? assessmentElement.textContent.toLowerCase() : '';
            const statusElement = row.querySelector('.badge');
            const status = statusElement ? statusElement.textContent.toLowerCase() : '';
            const consultationDateElement = row.querySelector('.consultation-date .fw-bold');
            const consultationDate = consultationDateElement ? consultationDateElement.textContent : '';

            let showRow = true;

            // Search filter
            if (searchTerm && !patientName.includes(searchTerm) && 
                !chiefComplaint.includes(searchTerm) && !assessment.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusValue && !status.includes(statusValue)) {
                showRow = false;
            }

            // Date filter
            if (dateValue && consultationDate) {
                const filterDate = new Date(dateValue);
                const consultationDateObj = new Date(consultationDate);
                if (filterDate.toDateString() !== consultationDateObj.toDateString()) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });

        // Update result count if needed
        updateResultCount(visibleCount);
    }

    function updateResultCount(count) {
        const totalRows = tableRows.length;
        const resultInfo = document.querySelector('.text-muted small');
        if (resultInfo && count !== totalRows) {
            resultInfo.innerHTML = `Showing ${count} of ${totalRows} consultations (filtered)`;
        }
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTableClientSide();
            debounceSubmit();
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            filterTableClientSide();
            immediateSubmit();
        });
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            filterTableClientSide();
            immediateSubmit();
        });
    }

    // Initialize client-side filtering on page load
    if (tableRows.length > 0) {
        filterTableClientSide();
    }
});
</script>

<!-- Enhanced Styles -->
<style>
.table th {
    font-weight: 600;
    font-size: 0.95em;
    border-top: none;
}

.table td {
    vertical-align: middle;
    font-size: 0.9em;
}

.consultation-date strong {
    color: #495057;
}

.patient-info strong {
    color: #0d6efd;
}

.chief-complaint {
    max-width: 200px;
    font-size: 0.9em;
}

.assessment {
    max-width: 180px;
    font-size: 0.9em;
}

.follow-up-date {
    white-space: nowrap;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85em;
    }
    
    .chief-complaint, .assessment {
        max-width: 150px;
    }
}
</style>
@endsection
