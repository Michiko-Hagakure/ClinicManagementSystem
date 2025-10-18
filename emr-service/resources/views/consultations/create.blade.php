@php
    // Check if user is a doctor to use appropriate layout
    $userRole = session('user_role') ?? session('role', '');
    $layout = $userRole === 'doctor' ? 'layouts.doctor' : 'layouts.app';
    $title = $userRole === 'doctor' ? 'New Consultation - Doctor Portal' : 'New Consultation';
@endphp

@extends($layout)

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">New Consultation</h1>
            <small class="text-muted">{{ request()->is('doctor/*') ? 'Doctor Portal - Patient Consultation Form' : 'Mary Angels Diagnostic Clinic - Patient Consultation Form' }}</small>
        </div>
        @if(request()->is('doctor/*'))
            <a href="{{ route('doctor.my-consultations') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to My Consultations
            </a>
        @else
        <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Consultations
        </a>
        @endif
    </div>

    <!-- Consultation Form -->
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 bg-success text-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle me-3 p-2">
                            <i class="bi bi-file-earmark-medical text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Patient Consultation Form</h5>
                            <small>Complete consultation record with vital signs and clinical notes</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ request()->is('doctor/*') ? route('doctor.consultations.store') : route('consultations.store') }}" method="POST" id="consultationForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="patient_id" class="form-label">Patient *</label>
                                <select name="patient_id" id="patient_id" class="form-select" required>
                                    <option value="">Select a patient</option>
                                    @if(isset($patients) && $patients->count() > 0)
                                        @foreach($patients as $p)
                                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->full_name }} (ID: {{ $p->patient_code }}) - {{ $p->age ?? 'N/A' }}yrs, {{ $p->gender ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <!-- Fallback: Options will be populated by Select2 AJAX -->
                                    @endif
                                </select>
                                @error('patient_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Consultation Date *</label>
                                <div class="custom-date-picker position-relative">
                                    <input type="text" 
                                           name="date_display" 
                                           id="consultation_date_display"
                                           class="form-control form-control-lg @error('date') is-invalid @enderror" 
                                           placeholder="Click to select date"
                                           style="font-size: 1.1em; padding-right: 3rem; cursor: pointer;"
                                           readonly
                                           required>
                                    <input type="hidden" name="date" id="consultation_date_input" value="{{ old('date', date('Y-m-d')) }}">
                                    <div class="calendar-icon-trigger" id="consultation-calendar-trigger" data-bs-toggle="modal" data-bs-target="#consultationCalendarModal" style="cursor: pointer;">
                                        <i class="bi bi-calendar3 text-success"></i>
                                    </div>
                                </div>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Vital Signs Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-heart-pulse me-2"></i>Vital Signs
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Blood Pressure *</label>
                                <input type="text" 
                                       name="bp" 
                                       class="form-control form-control-lg @error('bp') is-invalid @enderror" 
                                       value="{{ old('bp') }}" 
                                       placeholder="120/80"
                                       style="font-size: 1.1em; text-align: center;"
                                       required>
                                <small class="text-muted">e.g., 120/80</small>
                                @error('bp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Temperature (°C) *</label>
                                <input type="number" 
                                       name="temparature" 
                                       class="form-control form-control-lg @error('temparature') is-invalid @enderror" 
                                       value="{{ old('temparature') }}" 
                                       step="0.1" 
                                       min="30" 
                                       max="50"
                                       placeholder="36.5"
                                       style="font-size: 1.1em; text-align: center;"
                                       required>
                                <small class="text-muted">Normal: 36.1-37.2°C</small>
                                @error('temparature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Weight (kg) *</label>
                                <input type="number" 
                                       name="weight" 
                                       class="form-control form-control-lg @error('weight') is-invalid @enderror" 
                                       value="{{ old('weight') }}" 
                                       step="0.1" 
                                       min="1" 
                                       max="500"
                                       placeholder="70.0"
                                       style="font-size: 1.1em; text-align: center;"
                                       required>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Oxygen Saturation (%) *</label>
                                <input type="number" 
                                       name="o2" 
                                       class="form-control form-control-lg @error('o2') is-invalid @enderror" 
                                       value="{{ old('o2') }}" 
                                       min="50" 
                                       max="100"
                                       placeholder="98"
                                       style="font-size: 1.1em; text-align: center;"
                                       required>
                                <small class="text-muted">Normal: 95-100%</small>
                                @error('o2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Pulse Rate (bpm) *</label>
                                <input type="number" 
                                       name="pr" 
                                       class="form-control form-control-lg @error('pr') is-invalid @enderror" 
                                       value="{{ old('pr') }}" 
                                       min="30" 
                                       max="200"
                                       placeholder="72"
                                       style="font-size: 1.1em; text-align: center;"
                                       required>
                                <small class="text-muted">Normal: 60-100 bpm</small>
                                @error('pr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Consultation Status</label>
                                <div id="status_display" class="p-3 bg-warning bg-opacity-10 border border-warning rounded" style="font-size: 1.1em;">
                                    <i class="bi bi-clock-fill text-warning me-2"></i>
                                    <span class="text-warning fw-bold">Pending</span>
                                    <small class="d-block text-muted mt-1">Complete all required fields to finish consultation</small>
                                </div>
                                <input type="hidden" name="status" id="status_input" value="pending">
                                <div id="missing_fields" class="mt-2" style="display: none;">
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <span id="missing_fields_text">Missing: </span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Follow-up Date</label>
                                <div class="custom-date-picker position-relative">
                                    <input type="text" 
                                           name="follow_up_date_display" 
                                           id="follow_up_date_display"
                                           class="form-control form-control-lg @error('follow_up_date') is-invalid @enderror"
                                           style="font-size: 1.1em; padding-right: 3rem; cursor: pointer;"
                                           readonly>
                                    <input type="hidden" name="follow_up_date" id="follow_up_date_input" value="{{ old('follow_up_date') }}">
                                    <div class="calendar-icon-trigger" id="followup-calendar-trigger" data-bs-toggle="modal" data-bs-target="#followupCalendarModal" style="cursor: pointer;">
                                        <i class="bi bi-calendar-plus text-info"></i>
                                    </div>
                                </div>
                                <small class="text-muted">If follow-up needed</small>
                                @error('follow_up_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Clinical Information Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-clipboard-pulse me-2"></i>Clinical Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Chief Complaint *</label>
                                <textarea name="chief_complaint" 
                                          class="form-control form-control-lg @error('chief_complaint') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Patient's main concern or reason for visit..."
                                          style="font-size: 1.1em;"
                                          required>{{ old('chief_complaint') }}</textarea>
                                @error('chief_complaint')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        

                        
                        <!-- Additional Notes Section -->
                        <div class="row mb-4 mt-4">
                            <div class="col-12">
                                <h6 class="text-success border-bottom pb-2 mb-3">
                                    <i class="bi bi-journal-text me-2"></i>Additional Notes
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size: 1.1em;">Additional Consultation Notes</label>
                                <textarea name="consultation_notes" 
                                          class="form-control form-control-lg @error('consultation_notes') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Any additional notes, observations, or comments..."
                                          style="font-size: 1.1em;">{{ old('consultation_notes') }}</textarea>
                                @error('consultation_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    @if(request()->is('doctor/*'))
                                        <a href="{{ route('doctor.my-consultations') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                    @else
                                    <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    @endif
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-file-earmark-check me-2"></i>Save Consultation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="card mt-3 border-info">
                <div class="card-body">
                    <h6 class="text-info">
                        <i class="bi bi-info-circle me-2"></i>Consultation Guidelines
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li>Complete all vital signs accurately for proper medical record</li>
                        <li>Record the patient's chief complaint or reason for visit</li>
                        <li>Add detailed consultation notes for future reference</li>
                        <li>Set follow-up dates when necessary for patient care</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consultation Date Calendar Modal -->
<div class="modal fade" id="consultationCalendarModal" tabindex="-1" aria-labelledby="consultationCalendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="consultationCalendarModalLabel">
                    <i class="bi bi-calendar3 me-2"></i>Select Consultation Date
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;">
                <!-- Custom Calendar Content -->
                <div id="consultation-calendar" class="custom-calendar-modal-compact">
                    <div class="calendar-header mb-3">
                        <button type="button" class="calendar-nav-btn" id="consultation-prev-month-btn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="calendar-dropdowns">
                            <select id="consultation-month-dropdown" class="form-select calendar-dropdown">
                                <option value="0">January</option>
                                <option value="1">February</option>
                                <option value="2">March</option>
                                <option value="3">April</option>
                                <option value="4">May</option>
                                <option value="5">June</option>
                                <option value="6">July</option>
                                <option value="7">August</option>
                                <option value="8">September</option>
                                <option value="9">October</option>
                                <option value="10">November</option>
                                <option value="11">December</option>
                            </select>
                            <select id="consultation-year-dropdown" class="form-select calendar-dropdown">
                                <!-- Years will be populated by JavaScript -->
                            </select>
                        </div>
                        <button type="button" class="calendar-nav-btn" id="consultation-next-month-btn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="calendar-weekdays mb-2">
                        <div class="calendar-weekday">Sun</div>
                        <div class="calendar-weekday">Mon</div>
                        <div class="calendar-weekday">Tue</div>
                        <div class="calendar-weekday">Wed</div>
                        <div class="calendar-weekday">Thu</div>
                        <div class="calendar-weekday">Fri</div>
                        <div class="calendar-weekday">Sat</div>
                    </div>
                    
                    <div class="calendar-days" id="consultation-calendar-days">
                        <!-- Days will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" id="consultation-today-btn">
                    <i class="bi bi-calendar-check me-1"></i>Today
                </button>
                <button type="button" class="btn btn-outline-secondary" id="consultation-clear-btn">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-success" id="consultation-done-btn" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Follow-up Date Calendar Modal -->
<div class="modal fade" id="followupCalendarModal" tabindex="-1" aria-labelledby="followupCalendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="followupCalendarModalLabel">
                    <i class="bi bi-calendar-plus me-2"></i>Select Follow-up Date
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;">
                <!-- Custom Calendar Content -->
                <div id="followup-calendar" class="custom-calendar-modal-compact">
                    <div class="calendar-header mb-3">
                        <button type="button" class="calendar-nav-btn" id="followup-prev-month-btn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="calendar-dropdowns">
                            <select id="followup-month-dropdown" class="form-select calendar-dropdown">
                                <option value="0">January</option>
                                <option value="1">February</option>
                                <option value="2">March</option>
                                <option value="3">April</option>
                                <option value="4">May</option>
                                <option value="5">June</option>
                                <option value="6">July</option>
                                <option value="7">August</option>
                                <option value="8">September</option>
                                <option value="9">October</option>
                                <option value="10">November</option>
                                <option value="11">December</option>
                            </select>
                            <select id="followup-year-dropdown" class="form-select calendar-dropdown">
                                <!-- Years will be populated by JavaScript -->
                            </select>
                        </div>
                        <button type="button" class="calendar-nav-btn" id="followup-next-month-btn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    <div class="calendar-weekdays mb-2">
                        <div class="calendar-weekday">Sun</div>
                        <div class="calendar-weekday">Mon</div>
                        <div class="calendar-weekday">Tue</div>
                        <div class="calendar-weekday">Wed</div>
                        <div class="calendar-weekday">Thu</div>
                        <div class="calendar-weekday">Fri</div>
                        <div class="calendar-weekday">Sat</div>
                    </div>
                    
                    <div class="calendar-days" id="followup-calendar-days">
                        <!-- Days will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-info" id="followup-today-btn">
                    <i class="bi bi-calendar-check me-1"></i>Today
                </button>
                <button type="button" class="btn btn-outline-secondary" id="followup-clear-btn">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-info" id="followup-done-btn" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Consultation Calendar JavaScript -->
<script>
// Consultation Calendar Variables
let consultationCurrentDate = new Date();
let consultationSelectedDate = null;
let followupCurrentDate = new Date();
let followupSelectedDate = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize consultation date (set to today by default)
    const today = new Date();
    const consultationDateInput = document.getElementById('consultation_date_input');
    const consultationDateDisplay = document.getElementById('consultation_date_display');
    
    if (consultationDateInput.value) {
        const existingDate = new Date(consultationDateInput.value);
        consultationSelectedDate = existingDate;
        consultationCurrentDate = new Date(existingDate);
        
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        consultationDateDisplay.value = existingDate.toLocaleDateString('en-US', options);
    } else {
        // Set today as default
        selectConsultationDate(today);
    }
    
    // Initialize calendars
    initializeConsultationCalendar();
    initializeFollowupCalendar();
    
    // Initialize year dropdowns
    initializeConsultationYearDropdown();
    initializeFollowupYearDropdown();
    
    // Add event listeners for consultation calendar
    const consultationMonthDropdown = document.getElementById('consultation-month-dropdown');
    const consultationYearDropdown = document.getElementById('consultation-year-dropdown');
    
    if (consultationMonthDropdown) {
        consultationMonthDropdown.addEventListener('change', function() {
            consultationCurrentDate.setMonth(parseInt(this.value));
            updateConsultationCalendarDisplay();
        });
    }
    
    if (consultationYearDropdown) {
        consultationYearDropdown.addEventListener('change', function() {
            consultationCurrentDate.setFullYear(parseInt(this.value));
            updateConsultationCalendarDisplay();
        });
    }
    
    // Add event listeners for follow-up calendar
    const followupMonthDropdown = document.getElementById('followup-month-dropdown');
    const followupYearDropdown = document.getElementById('followup-year-dropdown');
    
    if (followupMonthDropdown) {
        followupMonthDropdown.addEventListener('change', function() {
            followupCurrentDate.setMonth(parseInt(this.value));
            updateFollowupCalendarDisplay();
        });
    }
    
    if (followupYearDropdown) {
        followupYearDropdown.addEventListener('change', function() {
            followupCurrentDate.setFullYear(parseInt(this.value));
            updateFollowupCalendarDisplay();
        });
    }
    
    // Navigation buttons
    document.getElementById('consultation-prev-month-btn')?.addEventListener('click', () => changeConsultationMonth(-1));
    document.getElementById('consultation-next-month-btn')?.addEventListener('click', () => changeConsultationMonth(1));
    document.getElementById('consultation-today-btn')?.addEventListener('click', selectConsultationToday);
    document.getElementById('consultation-clear-btn')?.addEventListener('click', clearConsultationDate);
    
    document.getElementById('followup-prev-month-btn')?.addEventListener('click', () => changeFollowupMonth(-1));
    document.getElementById('followup-next-month-btn')?.addEventListener('click', () => changeFollowupMonth(1));
    document.getElementById('followup-today-btn')?.addEventListener('click', selectFollowupToday);
    document.getElementById('followup-clear-btn')?.addEventListener('click', clearFollowupDate);
    
    // Make input fields clickable
    consultationDateDisplay?.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('consultationCalendarModal'));
        modal.show();
    });
    
    document.getElementById('follow_up_date_display')?.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('followupCalendarModal'));
        modal.show();
    });
    
    // Check if patients are pre-loaded or if we need Select2 AJAX
    const patientSelect = $('#patient_id');
    const hasPreloadedOptions = patientSelect.find('option').length > 1; // More than just the empty option
    
    if (hasPreloadedOptions) {
        console.log('Using pre-loaded patients, no Select2 AJAX needed');
        // Just enhance with basic Select2 for search functionality
        patientSelect.select2({
            placeholder: 'Select a patient',
            allowClear: true,
            width: '100%'
        });
    } else {
        console.log('No pre-loaded patients, initializing Select2 with AJAX');
        // Initialize Select2 with AJAX for dynamic loading
        patientSelect.select2({
        placeholder: 'Search for a patient by name or ID',
        minimumInputLength: 1,
        ajax: {
            url: '/api/v1/patients',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                    console.log('Select2 searching for:', params.term);
                return {
                    q: params.term // search term
                };
            },
            processResults: function(data) {
                    console.log('Select2 received data:', data);
                    
                    if (!Array.isArray(data)) {
                        console.error('Expected array but received:', typeof data, data);
                        return { results: [] };
                    }
                    
                return {
                    results: data.map(function(patient) {
                            console.log('Processing patient:', patient);
                            const displayText = (patient.full_name || patient.first_name + ' ' + patient.last_name) + 
                                              ' (ID: ' + (patient.patient_code || patient.id) + ') - ' + 
                                              (patient.age || 'N/A') + ' yrs';
                        return {
                            id: patient.id,
                                text: displayText,
                            patient: patient
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: formatPatient,
        templateSelection: formatPatientSelection
        }).on('select2:open', function() {
            console.log('Select2 dropdown opened');
        }).on('select2:close', function() {
            console.log('Select2 dropdown closed');
        }).on('select2:loading', function() {
            console.log('Select2 loading data...');
        }).on('select2:loaded', function() {
            console.log('Select2 data loaded');
        });
    }

    function formatPatient(patient) {
        if (patient.loading) {
            return patient.text;
        }
        return $(`<span>${patient.text}</span>`);
    }

    function formatPatientSelection(patient) {
        return patient.text || 'Select a patient';
    }

    // Handle patient selection (works for both pre-loaded and AJAX)
    $('#patient_id').on('select2:select change', function(e) {
        if (e.type === 'select2:select') {
        const patient = e.params.data;
            $('#patient_id').val(patient.id).trigger('change');
        }
        updateStatusDisplay(); // Update status after patient selection
    });

    // Auto-update status based on form completion
    setupStatusMonitoring();

});

// Consultation Calendar Functions
function initializeConsultationCalendar() {
    updateConsultationCalendarDisplay();
}

function initializeConsultationYearDropdown() {
    const yearDropdown = document.getElementById('consultation-year-dropdown');
    if (!yearDropdown) return;
    
    const currentYear = new Date().getFullYear();
    yearDropdown.innerHTML = '';
    
    // For consultations, show current year ± 5 years
    for (let year = currentYear + 2; year >= currentYear - 5; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearDropdown.appendChild(option);
    }
    
    yearDropdown.value = currentYear;
}

function changeConsultationMonth(direction) {
    consultationCurrentDate.setMonth(consultationCurrentDate.getMonth() + direction);
    updateConsultationCalendarDisplay();
}

function updateConsultationCalendarDisplay() {
    const monthDropdown = document.getElementById('consultation-month-dropdown');
    const yearDropdown = document.getElementById('consultation-year-dropdown');
    
    if (monthDropdown) monthDropdown.value = consultationCurrentDate.getMonth();
    if (yearDropdown) yearDropdown.value = consultationCurrentDate.getFullYear();
    
    generateConsultationCalendarDays();
}

function generateConsultationCalendarDays() {
    const daysContainer = document.getElementById('consultation-calendar-days');
    daysContainer.innerHTML = '';
    
    const year = consultationCurrentDate.getFullYear();
    const month = consultationCurrentDate.getMonth();
    const today = new Date();
    
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = date.getDate();
        
        if (date.getMonth() !== month) {
            dayElement.classList.add('other-month');
        }
        
        const isToday = date.getFullYear() === today.getFullYear() && 
                       date.getMonth() === today.getMonth() && 
                       date.getDate() === today.getDate();
        
        if (isToday) dayElement.classList.add('today');
        
        if (consultationSelectedDate && isSameDate(date, consultationSelectedDate)) {
            dayElement.classList.add('selected');
        }
        
        dayElement.addEventListener('click', () => selectConsultationDate(date));
        daysContainer.appendChild(dayElement);
    }
}

function selectConsultationDate(date) {
    consultationSelectedDate = new Date(date);
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}`;
    
    document.getElementById('consultation_date_input').value = formattedDate;
    
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('consultation_date_display').value = date.toLocaleDateString('en-US', options);
    
    updateConsultationCalendarDisplay();
    updateConsultationStatus(); // Update status when date changes
}

function selectConsultationToday() {
    selectConsultationDate(new Date());
}

function clearConsultationDate() {
    consultationSelectedDate = null;
    document.getElementById('consultation_date_input').value = '';
    document.getElementById('consultation_date_display').value = '';
    updateConsultationCalendarDisplay();
    updateConsultationStatus();
}

// Follow-up Calendar Functions
function initializeFollowupCalendar() {
    updateFollowupCalendarDisplay();
}

function initializeFollowupYearDropdown() {
    const yearDropdown = document.getElementById('followup-year-dropdown');
    if (!yearDropdown) return;
    
    const currentYear = new Date().getFullYear();
    yearDropdown.innerHTML = '';
    
    // For follow-ups, show next 3 years (future dates)
    for (let year = currentYear + 3; year >= currentYear; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearDropdown.appendChild(option);
    }
    
    yearDropdown.value = currentYear;
}

function changeFollowupMonth(direction) {
    followupCurrentDate.setMonth(followupCurrentDate.getMonth() + direction);
    updateFollowupCalendarDisplay();
}

function updateFollowupCalendarDisplay() {
    const monthDropdown = document.getElementById('followup-month-dropdown');
    const yearDropdown = document.getElementById('followup-year-dropdown');
    
    if (monthDropdown) monthDropdown.value = followupCurrentDate.getMonth();
    if (yearDropdown) yearDropdown.value = followupCurrentDate.getFullYear();
    
    generateFollowupCalendarDays();
}

function generateFollowupCalendarDays() {
    const daysContainer = document.getElementById('followup-calendar-days');
    daysContainer.innerHTML = '';
    
    const year = followupCurrentDate.getFullYear();
    const month = followupCurrentDate.getMonth();
    const today = new Date();
    
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = date.getDate();
        
        if (date.getMonth() !== month) {
            dayElement.classList.add('other-month');
        }
        
        const isToday = date.getFullYear() === today.getFullYear() && 
                       date.getMonth() === today.getMonth() && 
                       date.getDate() === today.getDate();
        
        if (isToday) dayElement.classList.add('today');
        
        if (followupSelectedDate && isSameDate(date, followupSelectedDate)) {
            dayElement.classList.add('selected');
        }
        
        dayElement.addEventListener('click', () => selectFollowupDate(date));
        daysContainer.appendChild(dayElement);
    }
}

function selectFollowupDate(date) {
    followupSelectedDate = new Date(date);
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}`;
    
    document.getElementById('follow_up_date_input').value = formattedDate;
    
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('follow_up_date_display').value = date.toLocaleDateString('en-US', options);
    
    updateFollowupCalendarDisplay();
    updateStatusDisplay(); // Update status when follow-up date changes
}

function selectFollowupToday() {
    selectFollowupDate(new Date());
}

function clearFollowupDate() {
    followupSelectedDate = null;
    document.getElementById('follow_up_date_input').value = '';
    document.getElementById('follow_up_date_display').value = '';
    updateFollowupCalendarDisplay();
    updateStatusDisplay();
}

// Intelligent status update based on form completion
function updateStatusDisplay() {
    const statusDisplay = document.getElementById('status_display');
    const statusInput = document.getElementById('status_input');
    const missingFieldsDiv = document.getElementById('missing_fields');
    const missingFieldsText = document.getElementById('missing_fields_text');
    
    if (!statusDisplay || !statusInput) return;
    
    // Check required fields
    const requiredFields = [
        { name: 'patient_id', label: 'Patient', element: document.getElementById('patient_id') },
        { name: 'date', label: 'Date', element: document.getElementById('consultation_date_input') },
        { name: 'bp', label: 'Blood Pressure', element: document.querySelector('[name="bp"]') },
        { name: 'temparature', label: 'Temperature', element: document.querySelector('[name="temparature"]') },
        { name: 'weight', label: 'Weight', element: document.querySelector('[name="weight"]') },
        { name: 'o2', label: 'Oxygen Saturation', element: document.querySelector('[name="o2"]') },
        { name: 'pr', label: 'Pulse Rate', element: document.querySelector('[name="pr"]') },
        { name: 'chief_complaint', label: 'Chief Complaint', element: document.querySelector('[name="chief_complaint"]') }
    ];
    
    const missingFields = [];
    
    requiredFields.forEach(field => {
        if (field.element && (!field.element.value || field.element.value.trim() === '')) {
            missingFields.push(field.label);
        }
    });
    
    const followupDate = document.getElementById('follow_up_date_input').value;
    
    if (missingFields.length > 0) {
        // Pending - missing required fields
        statusDisplay.className = 'p-3 bg-warning bg-opacity-10 border border-warning rounded';
        statusDisplay.innerHTML = `
            <i class="bi bi-clock-fill text-warning me-2"></i>
            <span class="text-warning fw-bold">Pending</span>
            <small class="d-block text-muted mt-1">Complete all required fields to finish consultation</small>
        `;
        statusInput.value = 'pending';
        
        // Show missing fields
        missingFieldsText.textContent = 'Missing: ' + missingFields.join(', ');
        missingFieldsDiv.style.display = 'block';
        
    } else if (followupDate) {
        // Follow-up required - all fields complete with follow-up
        statusDisplay.className = 'p-3 bg-info bg-opacity-10 border border-info rounded';
        statusDisplay.innerHTML = `
            <i class="bi bi-calendar-event-fill text-info me-2"></i>
            <span class="text-info fw-bold">Follow-up Required</span>
            <small class="d-block text-muted mt-1">All fields complete - follow-up scheduled</small>
        `;
        statusInput.value = 'follow_up_required';
        missingFieldsDiv.style.display = 'none';
        
    } else {
        // Ready for doctor - all fields complete, no follow-up
        statusDisplay.className = 'p-3 bg-success bg-opacity-10 border border-success rounded';
        statusDisplay.innerHTML = `
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <span class="text-success fw-bold">Ready for Doctor</span>
            <small class="d-block text-muted mt-1">All fields complete - ready to save as pending for doctor review</small>
        `;
        statusInput.value = 'pending';
        missingFieldsDiv.style.display = 'none';
    }
}

// Setup real-time status monitoring
function setupStatusMonitoring() {
    // List of all required fields to monitor
    const fieldsToMonitor = [
        'patient_id', 'bp', 'temparature', 'weight', 'o2', 'pr', 'chief_complaint'
    ];
    
    // Add event listeners to all required fields
    fieldsToMonitor.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('input', updateStatusDisplay);
            field.addEventListener('change', updateStatusDisplay);
        }
    });
    
    // Monitor consultation date changes
    const consultationDateInput = document.getElementById('consultation_date_input');
    if (consultationDateInput) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    updateStatusDisplay();
                }
            });
        });
        observer.observe(consultationDateInput, { 
            attributes: true, 
            attributeFilter: ['value'] 
        });
        
        // Also listen for direct value changes
        consultationDateInput.addEventListener('input', updateStatusDisplay);
        consultationDateInput.addEventListener('change', updateStatusDisplay);
    }
    
    // Initial status check
    updateStatusDisplay();
}

// Utility Functions
function isSameDate(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}
</script>

<!-- Enhanced Senior-Friendly Styles -->
<style>
/* Custom Date Picker Container */
.custom-date-picker {
    position: relative;
}

.custom-date-picker input {
    transition: all 0.3s ease;
}

.custom-date-picker input:focus {
    border-color: #198754 !important;
    box-shadow: 0 0 15px rgba(25, 135, 84, 0.3) !important;
    transform: scale(1.02);
}

.calendar-icon-trigger {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.3em;
    cursor: pointer;
    z-index: 5;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.calendar-icon-trigger:hover {
    background-color: rgba(25, 135, 84, 0.1);
    transform: translateY(-50%) scale(1.1);
}

/* Custom Calendar Modal - Professional Design */
.custom-calendar-modal-compact {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Calendar Header */
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.calendar-nav-btn {
    background: #198754;
    color: white;
    border: none;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    font-size: 1.2em;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.calendar-nav-btn:hover {
    background: #157347;
    transform: scale(1.1);
}

/* Calendar Dropdowns */
.calendar-dropdowns {
    display: flex;
    gap: 15px;
    flex-grow: 1;
    justify-content: center;
    align-items: center;
}

.calendar-dropdown {
    font-size: 1.1em !important;
    font-weight: 600 !important;
    color: #198754 !important;
    border: 2px solid #198754 !important;
    border-radius: 8px !important;
    background-color: white !important;
    padding: 8px 15px !important;
    cursor: pointer !important;
    transition: all 0.3s ease;
    min-width: 130px;
}

.calendar-dropdown:focus {
    box-shadow: 0 0 10px rgba(25, 135, 84, 0.5) !important;
    border-color: #157347 !important;
    outline: none !important;
}

.calendar-dropdown:hover {
    background-color: #f8fff9 !important;
    border-color: #157347 !important;
    transform: translateY(-1px);
}

/* Calendar Weekdays */
.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 15px;
}

.calendar-weekday {
    text-align: center;
    font-weight: bold;
    color: #6c757d;
    font-size: 0.9em;
    padding: 10px 0;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

/* Calendar Days Grid */
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: 600;
    transition: all 0.3s ease;
    background: white;
    color: #495057;
    min-height: 45px;
    position: relative;
}

.calendar-day:hover {
    background: #e3f2fd;
    border-color: #198754;
    transform: scale(1.05);
    color: #198754;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.calendar-day.today {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
    font-weight: bold;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
}

.calendar-day.selected {
    background: #198754;
    color: white;
    border-color: #157347;
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(25, 135, 84, 0.4);
}

.calendar-day.other-month {
    opacity: 0.3;
    background: #f8f9fa;
    color: #adb5bd;
}

.calendar-day.other-month:hover {
    opacity: 0.6;
    background: #e9ecef;
}

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
    border-color: #198754 !important;
    box-shadow: 0 0 15px rgba(25, 135, 84, 0.3) !important;
    transform: scale(1.02);
    background-color: #f8fff9 !important;
}

/* Enhanced Select Dropdown Styling */
.form-select-lg {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 1rem center !important;
    background-size: 20px !important;
    cursor: pointer !important;
}

.form-select-lg:hover {
    border-color: #198754 !important;
    background-color: #f8fff9 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

.form-label {
    font-size: 1.1em !important;
    color: #495057 !important;
    margin-bottom: 0.75rem !important;
}

.btn-lg {
    font-size: 1.1em !important;
    padding: 0.75rem 2rem !important;
    border-radius: 0.5rem !important;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* High contrast for senior users */
.form-select option {
    padding: 8px;
    font-size: 1.1em;
}

.card-header {
    font-size: 1.2em !important;
}

/* Better visual feedback */
.invalid-feedback {
    font-size: 1em !important;
    font-weight: 500;
}

/* Larger touch targets for mobile */
@media (max-width: 768px) {
    .form-control-lg, .form-select-lg {
        font-size: 1.2em !important;
        padding: 1rem !important;
    }
    
    .btn-lg {
        font-size: 1.2em !important;
        padding: 1rem 2rem !important;
    }
}
</style>
@endsection
