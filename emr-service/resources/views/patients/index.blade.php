@extends('layouts.app')

@section('title', 'Patient Records')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 text-gray-800">Patient Records</h1>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Add New Patient
    </a>
</div>

<!-- Search Bar -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('patients.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search patients by name or contact number..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Patients List -->
<div class="card shadow-sm">
    <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            @if(request('search'))
                Search Results for "{{ request('search') }}"
            @else
                All Patients
            @endif
        </h6>
        <small class="text-muted">{{ $patients->total() }} patient(s) found</small>
    </div>
    <div class="card-body p-0">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2">Patient ID</th>
                            <th>Full Name</th>
                            <th>Age/Sex</th>
                            <th>Contact Number</th>
                            <th>Civil Status</th>
                            <th>Address</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td class="px-3 py-2">
                                    <span class="badge bg-primary">{{ $patient->patient_id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $patient->full_name }}</strong>
                                            <p class="card-text mb-1">
                                                <small class="text-muted">
                                                    DOB: 
                                                    @if ($patient->date_of_birth)
                                                        {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </small>
                                            </p>
                                            <p class="card-text mb-0">
                                                <small class="text-muted">{{ $patient->sex }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $patient->age }} years</span>
                                </td>
                                <td>
                                    <i class="bi bi-telephone me-1"></i>{{ $patient->contact_number }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $patient->civil_staus }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($patient->address, 30) }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="btn btn-outline-info" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Delete"
                                                onclick="confirmDelete({{ $patient->patient_id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-3 py-2 border-top">
                {{ $patients->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No patients found</h5>
                @if(request('search'))
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>View All Patients
                    </a>
                @else
                    <p class="text-muted">Start by adding your first patient</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add First Patient
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this patient record?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will also delete all associated consultations and lab results.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Patient</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(patientId) {
    const form = document.getElementById('deleteForm');
    form.action = `/patients/${patientId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
