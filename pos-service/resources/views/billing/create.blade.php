@extends('layouts.app')

@section('title', 'New Medical Bill - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Medical Bill</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Patient Billing System</small>
        </div>
        <div>
            <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#patientSearchModal">
                <i class="bi bi-search me-2"></i>Search Patient
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>Print Invoice
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Patient Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-person-heart me-2"></i>Patient Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Patient Search</label>
                                <div class="position-relative">
                                    <input type="text" id="patient-search" class="form-control form-control-lg" placeholder="Type patient name, ID, or phone..." autocomplete="off">
                                    <input type="hidden" id="selected-patient-id" name="patient_id">
                                    <div id="patient-dropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Patient Code</label>
                                <input type="text" id="patient-code" class="form-control form-control-lg" placeholder="Auto-filled when patient selected" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="text" id="patient-dob" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" id="patient-phone" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Insurance</label>
                                <input type="text" id="patient-insurance" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="selected-patient-info" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Selected Patient:</strong> <span id="patient-full-name"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="clearPatientSelection()">
                                    <i class="bi bi-x"></i> Clear Selection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Services Selection -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-hospital me-2"></i>Medical Services & Billing
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Service Categories -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary active" data-category="consultations">
                                    <i class="bi bi-stethoscope me-2"></i>Consultations
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-category="diagnostics">
                                    <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostics
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-category="medications">
                                    <i class="bi bi-capsule-pill me-2"></i>Medications
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Consultation Services -->
                    <div id="consultations-services" class="service-category">
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-stethoscope me-2"></i>Consultation Services
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="general-consultation" data-price="500">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-check text-primary fa-2x mb-2"></i>
                                        <h6>General Consultation</h6>
                                        <p class="text-muted small mb-2">Doctor consultation and check-up</p>
                                        <h5 class="text-success">₱500.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="specialist-consultation" data-price="800">
                                    <div class="card-body text-center">
                                        <i class="bi bi-hospital text-info fa-2x mb-2"></i>
                                        <h6>Specialist Consultation</h6>
                                        <p class="text-muted small mb-2">Specialized medical consultation</p>
                                        <h5 class="text-success">₱800.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="follow-up-consultation" data-price="300">
                                    <div class="card-body text-center">
                                        <i class="bi bi-arrow-repeat text-warning fa-2x mb-2"></i>
                                        <h6>Follow-up Visit</h6>
                                        <p class="text-muted small mb-2">Follow-up consultation visit</p>
                                        <h5 class="text-success">₱300.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Diagnostic Services -->
                    <div id="diagnostics-services" class="service-category d-none">
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic Services
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="laboratory-tests" data-price="450">
                                    <div class="card-body text-center">
                                        <i class="bi bi-droplet text-danger fa-2x mb-2"></i>
                                        <h6>Laboratory Tests</h6>
                                        <p class="text-muted small mb-2">Blood work, urine tests, etc.</p>
                                        <h5 class="text-success">₱450.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="xray" data-price="650">
                                    <div class="card-body text-center">
                                        <i class="bi bi-x-ray text-secondary fa-2x mb-2"></i>
                                        <h6>X-ray Imaging</h6>
                                        <p class="text-muted small mb-2">Radiological imaging services</p>
                                        <h5 class="text-success">₱650.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="ultrasound" data-price="1200">
                                    <div class="card-body text-center">
                                        <i class="bi bi-soundwave text-info fa-2x mb-2"></i>
                                        <h6>Ultrasound</h6>
                                        <p class="text-muted small mb-2">Ultrasound imaging</p>
                                        <h5 class="text-success">₱1,200.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="ecg" data-price="350">
                                    <div class="card-body text-center">
                                        <i class="bi bi-heart-pulse text-danger fa-2x mb-2"></i>
                                        <h6>ECG/EKG</h6>
                                        <p class="text-muted small mb-2">Electrocardiogram</p>
                                        <h5 class="text-success">₱350.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="ct-scan" data-price="3500">
                                    <div class="card-body text-center">
                                        <i class="bi bi-cpu text-primary fa-2x mb-2"></i>
                                        <h6>CT Scan</h6>
                                        <p class="text-muted small mb-2">Computed Tomography</p>
                                        <h5 class="text-success">₱3,500.00</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card service-card" data-service="mri" data-price="8000">
                                    <div class="card-body text-center">
                                        <i class="bi bi-magnet text-success fa-2x mb-2"></i>
                                        <h6>MRI</h6>
                                        <p class="text-muted small mb-2">Magnetic Resonance Imaging</p>
                                        <h5 class="text-success">₱8,000.00</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medications -->
                    <div id="medications-services" class="service-category d-none">
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-capsule-pill me-2"></i>Medications
                        </h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search medications...">
                                </div>
                                <p class="text-muted">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Search and add prescribed medications to the bill. Medication prices will be calculated based on quantity and dosage.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow sticky-top">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-receipt me-2"></i>Bill Summary
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Selected Services -->
                    <div id="selected-services">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">No services selected</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Totals -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <span id="discount">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" class="text-success">₱0.00</strong>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <select class="form-select">
                            <option>Cash</option>
                            <option>Credit Card</option>
                            <option>GCash</option>
                            <option>PayMaya</option>
                            <option>Insurance</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Process Payment
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-printer me-2"></i>Print Invoice
                        </button>
                        <button class="btn btn-outline-danger">
                            <i class="bi bi-x-circle me-2"></i>Clear Bill
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Patient Search Modal -->
<div class="modal fade" id="patientSearchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search by name, ID, or phone number...">
                </div>
                
                <!-- Sample patient results -->
                <div class="list-group">
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1">Maria Santos</h6>
                                <p class="mb-1">Patient ID: P001 | Phone: 0917-123-4567</p>
                                <small>DOB: 1985-05-15 | Age: 38</small>
                            </div>
                            <button class="btn btn-primary btn-sm">Select</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.service-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.service-card:hover {
    border-color: #16a085;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(22, 160, 133, 0.2);
}

.service-card.selected {
    border-color: #16a085;
    background-color: rgba(22, 160, 133, 0.1);
}

.fa-2x {
    font-size: 2em;
}

.sticky-top {
    top: 20px;
}

/* Patient search dropdown styles */
#patient-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    margin-top: 2px;
}

#patient-dropdown .dropdown-item {
    padding: 12px 16px;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    transition: background-color 0.15s ease-in-out;
}

#patient-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #16a085;
}

#patient-dropdown .dropdown-item:active,
#patient-dropdown .dropdown-item.active {
    background-color: #16a085;
    color: white;
}

.position-relative {
    position: relative;
}

/* Patient info alert styling */
#selected-patient-info .alert {
    border-left: 4px solid #16a085;
}

/* Loading animation for patient search */
.patient-loading {
    padding: 12px 16px;
    text-align: center;
    color: #6c757d;
}

.patient-loading::after {
    content: "⏳";
    margin-left: 5px;
}
</style>

<!-- JavaScript for billing functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedServices = [];
    let searchTimeout;
    let selectedPatient = null;
    
    // Patient search functionality
    const patientSearchInput = document.getElementById('patient-search');
    const patientDropdown = document.getElementById('patient-dropdown');
    const selectedPatientId = document.getElementById('selected-patient-id');
    
    patientSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hidePatientDropdown();
            return;
        }
        
        searchTimeout = setTimeout(() => {
            searchPatients(query);
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.position-relative')) {
            hidePatientDropdown();
        }
    });
    
    function searchPatients(query) {
        fetch(`/api/patients/search?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(patients => {
                showPatientDropdown(patients);
            })
            .catch(error => {
                console.error('Error searching patients:', error);
                hidePatientDropdown();
            });
    }
    
    function showPatientDropdown(patients) {
        if (patients.length === 0) {
            patientDropdown.innerHTML = '<div class="dropdown-item text-muted">No patients found</div>';
        } else {
            patientDropdown.innerHTML = patients.map(patient => `
                <div class="dropdown-item patient-option" data-patient-id="${patient.id}" style="cursor: pointer;">
                    <div class="fw-bold">${patient.first_name} ${patient.last_name}</div>
                    <small class="text-muted">ID: ${patient.patient_code}</small>
                </div>
            `).join('');
            
            // Add click handlers for patient options
            patientDropdown.querySelectorAll('.patient-option').forEach(option => {
                option.addEventListener('click', function() {
                    selectPatient(this.dataset.patientId);
                });
            });
        }
        
        patientDropdown.style.display = 'block';
    }
    
    function hidePatientDropdown() {
        patientDropdown.style.display = 'none';
    }
    
    function selectPatient(patientId) {
        fetch(`/api/patients/${patientId}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(patient => {
                selectedPatient = patient;
                populatePatientFields(patient);
                hidePatientDropdown();
            })
            .catch(error => {
                console.error('Error loading patient details:', error);
                alert('Unable to load patient details. Please try again or check if the EMR service is running.');
                hidePatientDropdown();
            });
    }
    
    function populatePatientFields(patient) {
        // Update search field and hidden ID
        patientSearchInput.value = patient.full_name || `${patient.first_name} ${patient.last_name}`;
        selectedPatientId.value = patient.id;
        
        // Populate patient fields
        document.getElementById('patient-code').value = patient.patient_code || `P${String(patient.id).padStart(4, '0')}`;
        document.getElementById('patient-dob').value = formatDate(patient.date_of_birth) || '';
        document.getElementById('patient-phone').value = patient.phone || patient.contact_number || '';
        document.getElementById('patient-insurance').value = patient.insurance_provider || 'None';
        
        // Show selected patient info
        document.getElementById('patient-full-name').textContent = patient.full_name || `${patient.first_name} ${patient.last_name}`;
        document.getElementById('selected-patient-info').style.display = 'block';
    }
    
    function clearPatientSelection() {
        selectedPatient = null;
        patientSearchInput.value = '';
        selectedPatientId.value = '';
        
        // Clear patient fields
        document.getElementById('patient-code').value = '';
        document.getElementById('patient-dob').value = '';
        document.getElementById('patient-phone').value = '';
        document.getElementById('patient-insurance').value = '';
        
        // Hide selected patient info
        document.getElementById('selected-patient-info').style.display = 'none';
        
        // Focus back on search
        patientSearchInput.focus();
    }
    
    // Make clearPatientSelection globally available
    window.clearPatientSelection = clearPatientSelection;
    
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString();
    }
    
    // Service category switching
    const categoryButtons = document.querySelectorAll('[data-category]');
    const serviceCategories = document.querySelectorAll('.service-category');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Hide all service categories
            serviceCategories.forEach(category => category.classList.add('d-none'));
            // Show selected category
            const categoryId = this.dataset.category + '-services';
            document.getElementById(categoryId).classList.remove('d-none');
        });
    });
    
    // Service selection
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            const serviceName = this.dataset.service;
            const servicePrice = parseFloat(this.dataset.price);
            
            if (this.classList.contains('selected')) {
                // Deselect service
                this.classList.remove('selected');
                selectedServices = selectedServices.filter(s => s.name !== serviceName);
            } else {
                // Select service
                this.classList.add('selected');
                selectedServices.push({
                    name: serviceName,
                    displayName: this.querySelector('h6').textContent,
                    price: servicePrice
                });
            }
            
            updateBillSummary();
        });
    });
    
    function updateBillSummary() {
        const servicesContainer = document.getElementById('selected-services');
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total');
        
        if (selectedServices.length === 0) {
            servicesContainer.innerHTML = '<div class="d-flex justify-content-between align-items-center mb-2"><span class="text-muted">No services selected</span></div>';
            subtotalElement.textContent = '₱0.00';
            totalElement.textContent = '₱0.00';
            return;
        }
        
        let subtotal = 0;
        let servicesHtml = '';
        
        selectedServices.forEach(service => {
            subtotal += service.price;
            servicesHtml += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <small class="text-muted">${service.displayName}</small>
                    </div>
                    <span>₱${service.price.toFixed(2)}</span>
                </div>
            `;
        });
        
        servicesContainer.innerHTML = servicesHtml;
        subtotalElement.textContent = '₱' + subtotal.toFixed(2);
        totalElement.textContent = '₱' + subtotal.toFixed(2);
    }
});
</script>
@endsection
