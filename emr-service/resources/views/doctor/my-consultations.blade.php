@extends('layouts.doctor')

@section('title', 'My Consultations - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0 text-gray-800">My Consultations</h1>
        <small class="text-muted">Doctor Portal - Consultation Records Management</small>
    </div>
    <div>
        <a href="{{ route('doctor.consultations.create') }}" class="btn btn-success me-2">
            <i class="bi bi-file-earmark-plus me-1"></i>New Consultation
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('doctor.my-consultations') }}">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by patient name, complaint, or diagnosis..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('doctor.my-consultations') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
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
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-0">Pending</h6>
                        <h3 class="mb-0">{{ $consultations->where('status', 'pending')->count() }}</h3>
                    </div>
                    <div class="bg-white rounded-circle p-2">
                        <i class="bi bi-clock text-warning"></i>
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
                        <h3 class="mb-0">{{ $consultations->where('consultation_date', today()->toDateString())->count() }}</h3>
                    </div>
                    <div class="bg-white rounded-circle p-2">
                        <i class="bi bi-calendar-check text-info"></i>
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
                        <h6 class="card-title mb-0">Completed</h6>
                        <h3 class="mb-0">{{ $consultations->where('status', 'completed')->count() }}</h3>
                    </div>
                    <div class="bg-white rounded-circle p-2">
                        <i class="bi bi-check-circle text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consultations List -->
<div class="card shadow-sm">
    <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
        <div class="text-muted">
            <strong>Consultation Records</strong>
            <small class="ms-2">{{ $consultations->total() }} consultation(s) found</small>
        </div>
    </div>
    <div class="card-body p-0">
        @if($consultations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2">Date</th>
                            <th class="py-2">Patient</th>
                            <th class="py-2">Chief Complaint</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultations as $consultation)
                        <tr>
                            <td class="py-2">
                                <div>
                                    <strong>{{ $consultation->consultation_date ? $consultation->consultation_date->format('M d, Y') : 'N/A' }}</strong>
                                </div>
                                <small class="text-muted">{{ $consultation->consultation_date ? $consultation->consultation_date->format('g:i A') : '' }}</small>
                            </td>
                            <td class="py-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person me-2 text-muted"></i>
                                    <div>
                                        <strong>{{ $consultation->patient ? $consultation->patient->full_name : 'Unknown Patient' }}</strong>
                                        @if($consultation->patient && $consultation->patient->patient_code)
                                        <br><small class="text-muted">ID: {{ $consultation->patient->patient_code }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-2">
                                <div style="max-width: 200px;">
                                    @if($consultation->chief_complaint)
                                        <span class="text-truncate d-block">{{ $consultation->chief_complaint }}</span>
                                    @else
                                        <span class="text-muted"><em>Not specified</em></span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2">
                                @if($consultation->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($consultation->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($consultation->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($consultation->status) }}</span>
                                @endif
                            </td>
                            <td class="py-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View/Edit Consultation -->
                                    @if($consultation->patient)
                                    <a href="{{ route('doctor.consultation', $consultation->patient->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="View/Edit Consultation">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                    
                                    <!-- View Full Details -->
                                    <a href="{{ route('consultations.show', $consultation->id) }}" 
                                       class="btn btn-outline-info" 
                                       title="View Details">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                    
                                    <!-- Patient History -->
                                    @if($consultation->patient)
                                    <a href="{{ route('patients.consultations', $consultation->patient->id) }}" 
                                       class="btn btn-outline-secondary" 
                                       title="Patient History">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-file-earmark-medical display-4"></i>
                    <h5 class="mt-3">No consultations found</h5>
                    @if(request()->hasAny(['search', 'status', 'date']))
                        <p>No consultations match your search criteria.</p>
                        <a href="{{ route('doctor.my-consultations') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>View All Consultations
                        </a>
                    @else
                        <p>No consultations recorded yet.</p>
                        <a href="{{ route('consultations.create') }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-plus me-1"></i>Create First Consultation
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($consultations->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $consultations->appends(request()->query())->links() }}
    </div>
@endif
@endsection
