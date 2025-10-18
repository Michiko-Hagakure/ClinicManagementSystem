@extends('layouts.app')

@section('title', 'Add New Patient')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Add New Patient</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Patient Registration</small>
        </div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Patients
        </a>
    </div>

    <!-- Patient Registration Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header py-3 bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle me-3 p-2">
                        <i class="bi bi-person-plus text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Patient Registration Form</h5>
                        <small>Complete all required fields marked with *</small>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4">
                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf
                    
                    <!-- Personal Information Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-person-badge me-2"></i>Personal Information
                            </h6>
                        </div>
                    </div>
                    
                    <!-- Name Fields (matching your form: Last Name, First Name) -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Last Name (Apelyido) *</label>
                            <input type="text" 
                                   name="last_name" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   value="{{ old('last_name') }}" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">First Name (Pangalan) *</label>
                            <input type="text" 
                                   name="first_name" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   value="{{ old('first_name') }}" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Middle Name</label>
                            <input type="text" 
                                   name="middle_name" 
                                   class="form-control @error('middle_name') is-invalid @enderror" 
                                   value="{{ old('middle_name') }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Date of Birth with Custom Senior-Friendly Calendar -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Date of Birth *</label>
                            <div class="custom-date-picker position-relative">
                                <input type="text" 
                                       name="date_of_birth_display" 
                                       id="birth_date_display"
                                       class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror" 
                                       placeholder="Click to select date"
                                       style="font-size: 1.1em; padding-right: 3rem; cursor: pointer;"
                                       readonly
                                       required>
                                <input type="hidden" name="date_of_birth" id="birth_date_input">
                                <div class="calendar-icon-trigger" id="calendar-trigger" data-bs-toggle="modal" data-bs-target="#calendarModal" style="cursor: pointer;">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                </div>
                            </div>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Age *</label>
                            <input type="number" 
                                   name="age" 
                                   id="age_input"
                                   class="form-control form-control-lg @error('age') is-invalid @enderror" 
                                   value="{{ old('age') }}" 
                                   min="0" 
                                   max="150" 
                                   style="font-size: 1.2em; text-align: center;"
                                   readonly>
                            <small class="text-muted">Automatically calculated</small>
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Sex *</label>
                            <select name="gender" class="form-select form-select-lg @error('gender') is-invalid @enderror" required style="font-size: 1.1em;">
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Contact Information Section -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-telephone me-2"></i>Contact Information
                            </h6>
                        </div>
                    </div>
                    
                    <!-- Address - Quezon City Districts -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Address (Quezon City) *</label>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">District *</label>
                            <select name="district" id="district_select" class="form-select form-select-lg @error('district') is-invalid @enderror" required style="font-size: 1.1em;">
                                <option value="">Select District</option>
                                <option value="District 1" {{ old('district') == 'District 1' ? 'selected' : '' }}>District 1 (Diliman, UP Campus)</option>
                                <option value="District 2" {{ old('district') == 'District 2' ? 'selected' : '' }}>District 2 (Cubao, Araneta)</option>
                                <option value="District 3" {{ old('district') == 'District 3' ? 'selected' : '' }}>District 3 (La Loma, Roosevelt)</option>
                                <option value="District 4" {{ old('district') == 'District 4' ? 'selected' : '' }}>District 4 (Novaliches, Fairview)</option>
                                <option value="District 5" {{ old('district') == 'District 5' ? 'selected' : '' }}>District 5 (Novaliches North)</option>
                                <option value="District 6" {{ old('district') == 'District 6' ? 'selected' : '' }}>District 6 (Commonwealth, Fairview)</option>
                            </select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Barangay *</label>
                            <select name="barangay" id="barangay_select" class="form-select form-select-lg @error('barangay') is-invalid @enderror" required style="font-size: 1.1em;">
                                <option value="">Select District First</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Street / House Number</label>
                            <input type="text" 
                                   name="street" 
                                   class="form-control form-control-lg @error('street') is-invalid @enderror" 
                                   value="{{ old('street') }}" 
                                   placeholder="e.g., 123 Rizal Street, Block 5 Lot 10"
                                   style="font-size: 1.1em;">
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Zip Code</label>
                            <input type="text" 
                                   name="zip_code" 
                                   id="zip_code_input"
                                   class="form-control form-control-lg @error('zip_code') is-invalid @enderror" 
                                   value="{{ old('zip_code') }}" 
                                   placeholder="Auto-filled"
                                   readonly
                                   style="font-size: 1.1em; text-align: center; background-color: #e9ecef; font-weight: 600; color: #198754;">
                            <small class="text-muted">Automatically calculated</small>
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Hidden field to store complete address -->
                    <input type="hidden" name="address" id="complete_address">
                    
                    <!-- Contact Number and Civil Status -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Contact Number *</label>
                            <input type="tel" 
                                   name="contact_number" 
                                   class="form-control form-control-lg @error('contact_number') is-invalid @enderror" 
                                   value="{{ old('contact_number') }}" 
                                   placeholder="09355102086"
                                   pattern="[0-9]{11}"
                                   maxlength="11"
                                   style="font-size: 1.2em; text-align: center; letter-spacing: 1px;"
                                   required>
                            <small class="text-muted">11-digit mobile number</small>
                            @error('contact_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 1.1em;">Civil Status *</label>
                            <select name="civil_status" class="form-select form-select-lg @error('civil_status') is-invalid @enderror" required style="font-size: 1.1em;">
                                <option value="">Select Civil Status</option>
                                <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('civil_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-person-plus me-2"></i>Register Patient
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
                    <i class="bi bi-info-circle me-2"></i>Registration Guidelines
                </h6>
                <ul class="small text-muted mb-0">
                    <li>Ensure all required fields (*) are completed accurately</li>
                    <li>Double-check contact number for follow-up communications</li>
                    <li>Address should be complete for emergency contact purposes</li>
                    <li>Age should match the date of birth provided</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Senior-Friendly Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="max-height: 90vh; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="calendarModalLabel">
                    <i class="bi bi-calendar3 me-2"></i>Select Date of Birth
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="max-height: 60vh; overflow-y: auto;">
                <!-- Custom Calendar Content -->
                <div id="custom-calendar" class="custom-calendar-modal-compact">
                    <div class="calendar-header mb-3">
                        <button type="button" class="calendar-nav-btn" id="prev-month-btn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="calendar-dropdowns">
                            <select id="month-dropdown" class="form-select calendar-dropdown">
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
                            <select id="year-dropdown" class="form-select calendar-dropdown">
                                <!-- Years will be populated by JavaScript -->
                            </select>
                        </div>
                        <button type="button" class="calendar-nav-btn" id="next-month-btn">
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
                    
                    <div class="calendar-days" id="calendar-days">
                        <!-- Days will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" id="today-btn">
                    <i class="bi bi-calendar-check me-1"></i>Today
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clear-btn">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-primary" id="done-btn" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-1"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Senior-Friendly JavaScript with Custom Calendar -->
<script>
// Custom Calendar for Senior Users
let currentDate = new Date();
let selectedDate = null;

document.addEventListener('DOMContentLoaded', function() {
    const birthDateInput = document.getElementById('birth_date_input');
    const birthDateDisplay = document.getElementById('birth_date_display');
    const ageInput = document.getElementById('age_input');
    
    // Initialize calendar
    initializeCustomCalendar();
    
    // Initialize year dropdown
    initializeYearDropdown();
    
    // Add event listeners for dropdowns
    const monthDropdown = document.getElementById('month-dropdown');
    const yearDropdown = document.getElementById('year-dropdown');
    
    if (monthDropdown) {
        monthDropdown.addEventListener('change', function() {
            currentDate.setMonth(parseInt(this.value));
            updateCalendarDisplay();
        });
    }
    
    if (yearDropdown) {
        yearDropdown.addEventListener('change', function() {
            currentDate.setFullYear(parseInt(this.value));
            updateCalendarDisplay();
        });
    }
    
    // Calculate age when date changes
    function calculateAge() {
        if (birthDateInput.value) {
            const birthDate = new Date(birthDateInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            ageInput.value = age >= 0 ? age : '';
        } else {
            ageInput.value = '';
        }
    }
    
    // Listen for changes to the hidden birth date input
    birthDateInput.addEventListener('change', calculateAge);
    
    // Initialize calendar on page load
    calculateAge(); // Calculate age if date already exists
    
    // Also make the input clickable to open modal
    if (birthDateDisplay) {
        birthDateDisplay.addEventListener('click', function() {
            const calendarModal = new bootstrap.Modal(document.getElementById('calendarModal'));
            calendarModal.show();
        });
    }
    
    // Add event listeners for calendar navigation and action buttons
    const prevMonthBtn = document.getElementById('prev-month-btn');
    const nextMonthBtn = document.getElementById('next-month-btn');
    const todayBtn = document.getElementById('today-btn');
    const clearBtn = document.getElementById('clear-btn');
    const doneBtn = document.getElementById('done-btn');
    
    if (prevMonthBtn) prevMonthBtn.addEventListener('click', () => changeMonth(-1));
    if (nextMonthBtn) nextMonthBtn.addEventListener('click', () => changeMonth(1));
    if (todayBtn) todayBtn.addEventListener('click', selectToday);
    if (clearBtn) clearBtn.addEventListener('click', clearDate);
    if (doneBtn) doneBtn.addEventListener('click', () => {
        const calendarModal = bootstrap.Modal.getInstance(document.getElementById('calendarModal'));
        if (calendarModal) calendarModal.hide();
    });
    
    // Update calendar when modal is shown
    document.getElementById('calendarModal').addEventListener('shown.bs.modal', function() {
        updateCalendarDisplay();
    });
    
    // Quezon City Barangay Data
    const qcBarangays = {
        'District 1': [
            'Alicia', 'Bagong Pag-asa', 'Bahay Toro', 'Balingasa', 'Bungad', 'Damar', 'Damayan',
            'Del Monte', 'Katipunan', 'Lourdes', 'Maharlika', 'Malaya', 'Manresa', 'Mariblo',
            'Masambong', 'N.S. Amoranto', 'Nayong Kanluran', 'Paang Bundok', 'Pag-ibig sa Nayon',
            'Paltok', 'Paraiso', 'Phil-Am', 'Project 6', 'Ramon Magsaysay', 'Saint Peter',
            'Salvacion', 'San Antonio', 'San Isidro Labrador', 'San Jose', 'Santa Cruz',
            'Santa Teresita', 'Santo Cristo', 'Santo Niño', 'Sienna', 'Talayan', 'Unang Sigaw',
            'Veterans Village', 'West Triangle'
        ],
        'District 2': [
            'Bagong Silangan', 'Batasan Hills', 'Commonwealth', 'Holy Spirit', 'Payatas'
        ],
        'District 3': [
            'Amihan', 'Bagumbayan', 'Bagumbuhay', 'Bayanihan', 'Blue Ridge A', 'Blue Ridge B',
            'Camp Aguinaldo', 'Claro', 'Dioquino Zobel', 'Duyan-duyan', 'E. Rodriguez',
            'Escopa', 'Libis', 'Loyola Heights', 'Milagrosa', 'Pansol', 'Quirino 2-A',
            'Quirino 2-B', 'Quirino 2-C', 'Quirino 3-A', 'San Roque', 'Silangan',
            'Socorro', 'Tagumpay', 'Ugong Norte', 'Villa Maria Clara', 'White Plains'
        ],
        'District 4': [
            'Bagong Lipunan ng Crame', 'Botocan', 'Central', 'Cruzada', 'Dioquino Zobel',
            'Dona Imelda', 'Dona Josefa', 'Don Manuel', 'Duyan-duyan', 'East Kamias',
            'Immaculate Conception', 'Kalusugan', 'Kamias', 'Kamuning', 'Kaunlaran',
            'Krus na Ligas', 'Laging Handa', 'Malaya', 'Marilag', 'Obrero',
            'Old Capitol Site', 'Paligsahan', 'Pinagkaisahan', 'Pinyahan', 'Project 7',
            'Project 8', 'Roxas', 'Sacred Heart', 'Saint Ignatius', 'San Martin de Porres',
            'Sikatuna Village', 'South Triangle', 'Teachers Village East', 'Teachers Village West',
            'U.P. Campus', 'U.P. Village', 'Valencia', 'West Kamias', 'West Triangle'
        ],
        'District 5': [
            'Bagbag', 'Capri', 'Fairview', 'Greater Lagro', 'Gulod', 'Kaligayahan',
            'Nagkaisang Nayon', 'North Fairview', 'Novaliches Proper', 'Pasong Putik Proper',
            'San Agustin', 'San Bartolome', 'Santa Lucia', 'Santa Monica'
        ],
        'District 6': [
            'Apolonio Samson', 'Baesa', 'Balong Bato', 'Culiat', 'New Era',
            'Pasong Tamo', 'Sangandaan', 'Sauyo', 'Talipapa', 'Tandang Sora'
        ]
    };
    
    // Quezon City Zip Code Mapping (Detailed Barangay Level)
    const barangayZipCodes = {
        // District 6 (Novaliches Area)
        'Apolonio Samson': '1117',
        'Baesa': '1128',
        'Balong Bato': '1116',
        'Culiat': '1128',
        'New Era': '1107',
        'Pasong Tamo': '1107',
        'Sangandaan': '1116',
        'Sauyo': '1116',
        'Talipapa': '1116',
        'Tandang Sora': '1116',
        
        // District 5 (Fairview/Greater Lagro Area)
        'Bagbag': '1116',
        'Capri': '1118',
        'Fairview': '1118',
        'Greater Lagro': '1118',
        'Gulod': '1117',
        'Kaligayahan': '1117',
        'Nagkaisang Nayon': '1118',
        'North Fairview': '1121',
        'Novaliches Proper': '1116',
        'Pasong Putik Proper': '1118',
        'San Agustin': '1117',
        'San Bartolome': '1116',
        'Santa Lucia': '1117',
        'Santa Monica': '1117',
        
        // District 1
        'Alicia': '1114',
        'Bagong Pag-asa': '1105',
        'Bahay Toro': '1106',
        'Balingasa': '1115',
        'Bungad': '1112',
        'Damar': '1115',
        'Damayan': '1104',
        'Del Monte': '1105',
        'Katipunan': '1105',
        'Manresa': '1117',
        'Mariblo': '1114',
        'Masambong': '1105',
        'N.S. Amoranto': '1113',
        'Nayong Kanluran': '1114',
        'Paang Bundok': '1114',
        'Pag-ibig sa Nayon': '1115',
        'Paltok': '1105',
        'Paraiso': '1103',
        'Phil-Am': '1104',
        'Project 6': '1100',
        'Salvacion': '1112',
        'San Antonio': '1102',
        'San Isidro Labrador': '1114',
        'San Jose': '1115',
        'Santa Cruz': '1104',
        'Santa Teresita': '1114',
        'Santo Cristo': '1105',
        'Santo Domingo': '1114',
        'Siena': '1114',
        'Talayan': '1104',
        'Vasra': '1105',
        'Veterans Village': '1105',
        'West Triangle': '1104',
        
        // District 2
        'Bagong Silangan': '1119',
        'Batasan Hills': '1126',
        'Commonwealth': '1121',
        'Holy Spirit': '1127',
        'Payatas': '1119',
        
        // District 3
        'Amihan': '1102',
        'Bayanihan': '1109',
        'Blue Ridge A': '1109',
        'Blue Ridge B': '1109',
        'Camp Aguinaldo': '1110',
        'Claro': '1102',
        'Dioquino Zobel': '1103',
        'Horseshoe': '1112',
        'Libis': '1110',
        'Loyola Heights': '1108',
        'Mangga': '1109',
        'Marilag': '1109',
        'Matandang Balara': '1119',
        'Milagrosa': '1109',
        'Pansol': '1109',
        'San Roque': '1109',
        'Silangan': '1102',
        'Socorro': '1109',
        'Tagumpay': '1109',
        'Ugong Norte': '1110',
        'Villa Maria Clara': '1109',
        'White Plains': '1110',
        
        // District 4
        'Bagong Lipunan ng Crame': '1112',
        'Botocan': '1105',
        'Central': '1100',
        'Cruzada': '1104',
        'Dona Imelda': '1113',
        'Dona Josefa': '1113',
        'Don Manuel': '1113',
        'Duyan-duyan': '1103',
        'East Kamias': '1102',
        'Immaculate Conception': '1111',
        'Kalusugan': '1100',
        'Kamias': '1102',
        'Kamuning': '1103',
        'Kaunlaran': '1111',
        'Krus na Ligas': '1101',
        'Laging Handa': '1103',
        'Malaya': '1101',
        'Obrero': '1103',
        'Old Capitol Site': '1100',
        'Paligsahan': '1103',
        'Pinagkaisahan': '1111',
        'Pinyahan': '1100',
        'Project 7': '1105',
        'Project 8': '1106',
        'Roxas': '1103',
        'Sacred Heart': '1103',
        'Saint Ignatius': '1109',
        'San Martin de Porres': '1111',
        'Sikatuna Village': '1101',
        'South Triangle': '1103',
        'Teachers Village East': '1101',
        'Teachers Village West': '1101',
        'U.P. Campus': '1101',
        'U.P. Village': '1101',
        'Valencia': '1112',
        'West Kamias': '1102'
    };
    
    // District default zip codes (fallback)
    const districtZipCodes = {
        'District 1': '1105',
        'District 2': '1121', 
        'District 3': '1109',
        'District 4': '1103',
        'District 5': '1118',
        'District 6': '1116'
    };
    
    // District and Barangay Selection
    const districtSelect = document.getElementById('district_select');
    const barangaySelect = document.getElementById('barangay_select');
    const completeAddressInput = document.getElementById('complete_address');
    const zipCodeInput = document.getElementById('zip_code_input');
    
    districtSelect.addEventListener('change', function() {
        const selectedDistrict = this.value;
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        
        if (selectedDistrict && qcBarangays[selectedDistrict]) {
            qcBarangays[selectedDistrict].forEach(function(barangay) {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });
            barangaySelect.disabled = false;
            
            // Set default district zip code
            if (districtZipCodes[selectedDistrict]) {
                zipCodeInput.value = districtZipCodes[selectedDistrict];
            }
        } else {
            barangaySelect.disabled = true;
            zipCodeInput.value = '';
        }
        updateCompleteAddress();
    });
    
    // Update zip code when barangay is selected
    barangaySelect.addEventListener('change', function() {
        const selectedBarangay = this.value;
        
        // Update zip code based on specific barangay
        if (selectedBarangay && barangayZipCodes[selectedBarangay]) {
            zipCodeInput.value = barangayZipCodes[selectedBarangay];
        }
        
        updateCompleteAddress();
    });
    
    // Update complete address when any address field changes
    function updateCompleteAddress() {
        const street = document.querySelector('input[name="street"]').value;
        const barangay = barangaySelect.value;
        const district = districtSelect.value;
        const zipCode = document.querySelector('input[name="zip_code"]').value;
        
        let address = '';
        if (street) address += street + ', ';
        if (barangay) address += 'Barangay ' + barangay + ', ';
        if (district) address += district + ', ';
        address += 'Quezon City';
        if (zipCode) address += ' ' + zipCode;
        
        completeAddressInput.value = address;
    }
    
    document.querySelector('input[name="street"]').addEventListener('input', updateCompleteAddress);
    document.querySelector('input[name="zip_code"]').addEventListener('input', updateCompleteAddress);
});

// Custom Calendar Functions for Senior Users (defined globally)
function initializeCustomCalendar() {
    updateCalendarDisplay();
}

function initializeYearDropdown() {
    const yearDropdown = document.getElementById('year-dropdown');
    if (!yearDropdown) return;
    
    const currentYear = new Date().getFullYear();
    const startYear = 1920; // For birth dates, start from 1920
    
    // Clear existing options
    yearDropdown.innerHTML = '';
    
    // Add years from current year down to 1920 (most recent first)
    for (let year = currentYear; year >= startYear; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearDropdown.appendChild(option);
    }
    
    // Set current year as default
    yearDropdown.value = currentYear;
}

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    updateCalendarDisplay();
}

function updateCalendarDisplay() {
    // Update dropdowns to match current date
    const monthDropdown = document.getElementById('month-dropdown');
    const yearDropdown = document.getElementById('year-dropdown');
    
    if (monthDropdown) {
        monthDropdown.value = currentDate.getMonth();
    }
    
    if (yearDropdown) {
        yearDropdown.value = currentDate.getFullYear();
    }
    
    generateCalendarDays();
}

function generateCalendarDays() {
    const daysContainer = document.getElementById('calendar-days');
    daysContainer.innerHTML = '';
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const today = new Date();
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay()); // Start from Sunday
    
    // Generate 6 weeks (42 days)
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = date.getDate();
        
        // Add classes for styling
        if (date.getMonth() !== month) {
            dayElement.classList.add('other-month');
        }
        
        // Check if this is today
        const isDateToday = date.getFullYear() === today.getFullYear() && 
                           date.getMonth() === today.getMonth() && 
                           date.getDate() === today.getDate();
        
        if (isDateToday) {
            dayElement.classList.add('today');
        }
        
        // Check if this date is selected
        if (selectedDate && isSameDate(date, selectedDate)) {
            dayElement.classList.add('selected');
        }
        
        // Add click handler
        dayElement.addEventListener('click', () => selectDate(date));
        
        daysContainer.appendChild(dayElement);
    }
}

function selectDate(date) {
    selectedDate = new Date(date);
    
    // Update hidden input (YYYY-MM-DD format)
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const formattedDate = `${year}-${month}-${day}`;
    
    const birthDateInput = document.getElementById('birth_date_input');
    const birthDateDisplay = document.getElementById('birth_date_display');
    const ageInput = document.getElementById('age_input');
    
    birthDateInput.value = formattedDate;
    
    // Update display input (friendly format)
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    birthDateDisplay.value = date.toLocaleDateString('en-US', options);
    
    // Calculate and display age immediately
    const today = new Date();
    let age = today.getFullYear() - date.getFullYear();
    const monthDiff = today.getMonth() - date.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
        age--;
    }
    
    ageInput.value = age >= 0 ? age : '';
    
    // Trigger change event for any other listeners
    birthDateInput.dispatchEvent(new Event('change'));
    
    // Update calendar display
    updateCalendarDisplay();
}

function selectToday() {
    selectDate(new Date());
}

function clearDate() {
    selectedDate = null;
    document.getElementById('birth_date_input').value = '';
    document.getElementById('birth_date_display').value = '';
    document.getElementById('age_input').value = '';
    updateCalendarDisplay();
}

function isToday(date) {
    const today = new Date();
    return isSameDate(date, today);
}

function isSameDate(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}

// Prevent double submission
document.querySelector('form').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn.disabled) {
        e.preventDefault();
        return false;
    }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering...';
});
</script>

<!-- Senior-Friendly Styles with Custom Calendar -->
<style>
/* Custom Date Picker Container */
.custom-date-picker {
    position: relative;
}

.custom-date-picker input {
    transition: all 0.3s ease;
}

.custom-date-picker input:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.3) !important;
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
    background-color: rgba(13, 110, 253, 0.1);
    transform: translateY(-50%) scale(1.1);
}

/* Custom Calendar Modal - Senior Friendly Design */
.custom-calendar-modal {
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Compact Calendar Modal - Fixed Height Design */
.custom-calendar-modal-compact {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Calendar Header */
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}

.calendar-nav-btn {
    background: #0d6efd;
    color: white;
    border: none;
    border-radius: 6px;
    width: 35px;
    height: 35px;
    font-size: 1.1em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.calendar-nav-btn:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}

/* Calendar Dropdowns */
.calendar-dropdowns {
    display: flex;
    gap: 10px;
    flex-grow: 1;
    justify-content: center;
    align-items: center;
}

.calendar-dropdown {
    font-size: 1.1em !important;
    font-weight: 600 !important;
    color: #0d6efd !important;
    border: 2px solid #0d6efd !important;
    border-radius: 6px !important;
    background-color: white !important;
    padding: 6px 12px !important;
    cursor: pointer !important;
    transition: all 0.3s ease;
    min-width: 120px;
}

.calendar-dropdown:focus {
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.5) !important;
    border-color: #0b5ed7 !important;
    outline: none !important;
}

.calendar-dropdown:hover {
    background-color: #f8f9ff !important;
    border-color: #0b5ed7 !important;
    transform: translateY(-1px);
}

/* Calendar Weekdays */
.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
    margin-bottom: 10px;
}

.calendar-weekday {
    text-align: center;
    font-weight: bold;
    color: #6c757d;
    font-size: 0.85em;
    padding: 6px 0;
    background: #f8f9fa;
    border-radius: 4px;
}

/* Calendar Days Grid */
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
    margin-bottom: 15px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1em;
    font-weight: 500;
    transition: all 0.3s ease;
    background: #f8f9fa;
    color: #495057;
    min-height: 35px;
}

.calendar-day:hover {
    background: #e3f2fd;
    border-color: #0d6efd;
    transform: scale(1.05);
    color: #0d6efd;
}

.calendar-day.today {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
    font-weight: bold;
}

.calendar-day.selected {
    background: #0d6efd;
    color: white;
    border-color: #0b5ed7;
    transform: scale(1.05);
}

.calendar-day.other-month {
    opacity: 0.4;
    background: #f8f9fa;
}

.calendar-day.other-month:hover {
    opacity: 0.7;
}

/* Calendar Footer */
.calendar-footer {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding-top: 10px;
    border-top: 2px solid #e9ecef;
}

.calendar-footer .btn {
    padding: 8px 16px;
    font-size: 1em;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.calendar-footer .btn:hover {
    transform: translateY(-2px);
}

/* Responsive Design for Mobile */
@media (max-width: 768px) {
    .custom-calendar-modal, .custom-calendar-modal-compact {
        font-size: 1em;
    }
    
    .calendar-day {
        min-height: 40px;
        font-size: 1.1em;
    }
    
    .calendar-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 1.2em;
    }
    
    .modal-content {
        max-height: 95vh !important;
    }
    
    .modal-body {
        max-height: 65vh !important;
    }
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
    border-color: #0d6efd !important;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.3) !important;
    transform: scale(1.02);
    background-color: #f8f9ff !important;
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
    border-color: #0d6efd !important;
    background-color: #f8f9ff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

/* Select Option Styling */
.form-select-lg option {
    padding: 8px 12px !important;
    font-size: 1.1em !important;
    background-color: #fff !important;
    color: #495057 !important;
}

.form-select-lg option:hover {
    background-color: #e3f2fd !important;
}

/* Disabled State */
.form-select-lg:disabled {
    background-color: #e9ecef !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.form-label {
    font-size: 1.1em !important;
    color: #495057 !important;
    margin-bottom: 0.75rem !important;
}

.btn {
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
    
    .btn {
        font-size: 1.2em !important;
        padding: 1rem 2rem !important;
    }
}
</style>
@endsection
