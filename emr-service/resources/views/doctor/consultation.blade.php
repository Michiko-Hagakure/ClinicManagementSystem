@extends('layouts.doctor')

@section('title', 'Patient Consultation - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Patient Consultation</h1>
        <p class="text-muted mb-0">{{ $patient->first_name }} {{ $patient->last_name }} ({{ $patient->patient_id }})</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('doctor.patient-queue') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to Queue
        </a>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-info">
            <i class="bi bi-file-earmark-person me-1"></i>Full Medical Record
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Patient Information -->
    <div class="col-lg-4">
        <div class="card shadow mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-person me-2"></i>Patient Information
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ asset('images/avatar.jpg') }}" alt="Patient Avatar" 
                         class="rounded-circle mb-2" width="80" height="80">
                    <h5 class="mb-1">{{ $patient->first_name }} {{ $patient->last_name }}</h5>
                    <small class="text-muted">ID: {{ $patient->id }}</small>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Age</small>
                        <div class="fw-bold">
                            @if($patient->date_of_birth)
                                {{ $patient->date_of_birth->age }} years
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Sex</small>
                        <div class="fw-bold">{{ $patient->gender ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <small class="text-muted">Contact Number</small>
                    <div class="fw-bold">{{ $patient->phone_number ?? 'N/A' }}</div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Address</small>
                    <div class="fw-bold">{{ $patient->address ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Lab Results -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-clipboard-pulse me-2"></i>Recent Lab Results
                </h6>
            </div>
            <div class="card-body">
                @if($recentLabResults && $recentLabResults->count() > 0)
                    @foreach($recentLabResults->take(3) as $labResult)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="fw-bold">{{ $labResult->test_type }}</small>
                            <br><small class="text-muted">{{ $labResult->test_date->format('M d, Y') }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $labResult->status === 'reviewed' ? 'success' : 'warning' }}">
                                {{ ucfirst($labResult->status) }}
                            </span>
                        </div>
                    </div>
                    @if(!$loop->last)<hr>@endif
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-clipboard-x text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">No recent lab results</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Consultation Form -->
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-file-earmark-medical me-2"></i>Today's Consultation
                </h6>
            </div>
            <div class="card-body">
                @if($todayConsultation)
                    <form action="{{ route('doctor.consultation.update', $todayConsultation) }}" method="POST">
                        @csrf
                        @method('PUT')
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No consultation record found for today. Please create one through the regular consultation system.
                    </div>
                @endif

                @if($todayConsultation)
                    <!-- Chief Complaint -->
                    <div class="mb-3">
                        <label for="chief_complaint" class="form-label fw-bold">Chief Complaint</label>
                        <textarea class="form-control" id="chief_complaint" name="chief_complaint" rows="3" 
                                  placeholder="What brings the patient in today?">{{ $todayConsultation->chief_complaint }}</textarea>
                    </div>

                    <!-- History of Present Illness -->
                    <div class="mb-3">
                        <label for="history_present_illness" class="form-label fw-bold">History of Present Illness</label>
                        <textarea class="form-control" id="history_present_illness" name="history_present_illness" rows="4" 
                                  placeholder="Detailed history of the current condition...">{{ $todayConsultation->history_present_illness }}</textarea>
                    </div>

                    <!-- Physical Examination -->
                    <div class="mb-3">
                        <label for="physical_examination" class="form-label fw-bold">Physical Examination</label>
                        <textarea class="form-control" id="physical_examination" name="physical_examination" rows="4" 
                                  placeholder="Physical examination findings...">{{ $todayConsultation->physical_examination }}</textarea>
                    </div>

                    <!-- Diagnosis -->
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label fw-bold">Diagnosis</label>
                        <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" 
                                  placeholder="Primary and secondary diagnoses...">{{ $todayConsultation->diagnosis }}</textarea>
                    </div>

                    <!-- Treatment Plan -->
                    <div class="mb-3">
                        <label for="treatment_plan" class="form-label fw-bold">Treatment Plan</label>
                        <textarea class="form-control" id="treatment_plan" name="treatment_plan" rows="4" 
                                  placeholder="Treatment recommendations and plan...">{{ $todayConsultation->treatment_plan }}</textarea>
                    </div>

                    <!-- Prescribed Medications -->
                    <div class="mb-3">
                        <label for="prescribed_medications" class="form-label fw-bold">Prescribed Medications</label>
                        <textarea class="form-control" id="prescribed_medications" name="prescribed_medications" rows="3" 
                                  placeholder="Medications prescribed with dosage and instructions...">{{ $todayConsultation->prescribed_medications }}</textarea>
                    </div>

                    <!-- Follow-up Instructions -->
                    <div class="mb-3">
                        <label for="follow_up_instructions" class="form-label fw-bold">Follow-up Instructions</label>
                        <textarea class="form-control" id="follow_up_instructions" name="follow_up_instructions" rows="3" 
                                  placeholder="When to return, what to watch for, etc...">{{ $todayConsultation->follow_up_instructions }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">Consultation Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $todayConsultation->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $todayConsultation->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $todayConsultation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Save Consultation
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="saveDraft()">
                            <i class="bi bi-save me-1"></i>Save Draft
                        </button>
                        <a href="{{ route('doctor.patient-queue') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Return to Queue
                        </a>
                    </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Patient History -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-clock-history me-2"></i>Medical History
                </h6>
            </div>
            <div class="card-body">
                @if($patientHistory && $patientHistory->count() > 0)
                    <div class="timeline">
                        @foreach($patientHistory as $consultation)
                        <div class="timeline-item mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                @if($consultation->consultation_date)
                                                    {{ $consultation->consultation_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">No date</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1"><strong>Chief Complaint:</strong> {{ $consultation->chief_complaint ?? 'Not recorded' }}</p>
                                            <p class="mb-1"><strong>Diagnosis:</strong> {{ $consultation->diagnosis ?? 'Not recorded' }}</p>
                                            @if($consultation->prescribed_medications)
                                                <p class="mb-0"><strong>Medications:</strong> {{ Str::limit($consultation->prescribed_medications, 100) }}</p>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ $consultation->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($consultation->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">No Previous Consultations</h6>
                        <p class="text-muted">This appears to be the patient's first visit.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function saveDraft() {
    // Auto-save functionality could be implemented here
    alert('Draft saved! (Feature to be implemented)');
}

// Auto-save every 2 minutes
setInterval(function() {
    // Implement auto-save functionality
    console.log('Auto-saving consultation draft...');
}, 120000);
</script>
@endsection
