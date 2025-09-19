@extends('layouts.app')

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
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search consultations by patient name, complaint, or diagnosis...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="follow_up_required">Follow-up Required</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" id="dateFilter" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <!-- Consultations List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-file-earmark-medical me-2"></i>Consultation Records
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
                                                    ID: {{ $consultation->patient->id }}
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

<!-- Search and Filter JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const dateValue = dateFilter.value;

        tableRows.forEach(row => {
            const patientName = row.querySelector('.patient-info strong').textContent.toLowerCase();
            const chiefComplaint = row.querySelector('.chief-complaint').textContent.toLowerCase();
            const assessment = row.querySelector('.assessment')?.textContent.toLowerCase() || '';
            const status = row.querySelector('.badge').textContent.toLowerCase();
            const consultationDate = row.querySelector('.consultation-date strong').textContent;

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
            if (dateValue) {
                const filterDate = new Date(dateValue).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
                if (!consultationDate.includes(filterDate.replace(',', ','))) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);
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
