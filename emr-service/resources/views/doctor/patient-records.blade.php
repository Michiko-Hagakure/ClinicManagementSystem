@extends('layouts.doctor')

@section('title', 'Patient Records - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0 text-gray-800">Patient Records</h1>
    <div class="text-muted">
        <small>Doctor Portal - Patient Management</small>
    </div>
</div>

<!-- Search Bar -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('doctor.patient-records') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search patients by name, patient code, or phone number..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('doctor.patient-records') }}" class="btn btn-outline-secondary">
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
        <div class="text-muted">
            <strong>All Patients</strong>
            <small class="ms-2">{{ $patients->total() }} patient(s) found</small>
        </div>
    </div>
    <div class="card-body p-0">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2">Patient ID</th>
                            <th class="py-2">Full Name</th>
                            <th class="py-2">Age/Sex</th>
                            <th class="py-2">Contact Number</th>
                            <th class="py-2">Civil Status</th>
                            <th class="py-2">Address</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td class="py-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person me-2 text-muted"></i>
                                    <strong>{{ $patient->patient_code }}</strong>
                                </div>
                                <small class="text-muted">DOB: {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</small>
                            </td>
                            <td class="py-2">
                                <strong>{{ $patient->full_name }}</strong>
                            </td>
                            <td class="py-2">
                                <span class="badge bg-info">{{ $patient->age ?? 'N/A' }} years</span>
                            </td>
                            <td class="py-2">
                                <i class="bi bi-telephone me-1"></i>
                                {{ $patient->phone_number ?? 'N/A' }}
                            </td>
                            <td class="py-2">{{ $patient->civil_status ?? 'N/A' }}</td>
                            <td class="py-2">
                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $patient->address ?? 'N/A' }}">
                                    {{ $patient->address ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Patient -->
                                    <a href="{{ route('doctor.view-patient', $patient->id) }}" 
                                       class="btn btn-outline-info" 
                                       title="View Patient Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- Start Consultation -->
                                    <a href="{{ route('doctor.consultation', $patient->id) }}" 
                                       class="btn btn-outline-success" 
                                       title="Start Consultation">
                                        <i class="bi bi-chat-dots"></i>
                                    </a>
                                    
                                    <!-- View History -->
                                    <a href="{{ route('patients.consultations', $patient->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Consultation History">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
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
                    <i class="bi bi-people display-4"></i>
                    <h5 class="mt-3">No patients found</h5>
                    @if(request('search'))
                        <p>No patients match your search criteria.</p>
                        <a href="{{ route('doctor.patient-records') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>View All Patients
                        </a>
                    @else
                        <p>No patients are registered in the system yet.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($patients->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $patients->appends(request()->query())->links() }}
    </div>
@endif
@endsection
