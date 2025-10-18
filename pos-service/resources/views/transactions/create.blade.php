@extends('layouts.app')

@section('title', 'New Transaction - POS System')

<style>
.cursor-pointer {
    cursor: pointer;
}

.service-item {
    transition: all 0.3s ease;
}

.service-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.service-item.border-success {
    border-width: 2px !important;
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.2);
}


.service-checkbox {
    transform: scale(1.2);
}

/* Laboratory Tests Styling */
.lab-test-item {
    transition: all 0.2s ease;
    background: white;
}

.lab-test-item:hover {
    background: #f8f9fa;
    border-color: #0d6efd !important;
}

.lab-test-item .form-check-input:checked ~ .form-check-label {
    background: #e3f2fd;
    border-radius: 4px;
}

.lab-test-checkbox:checked + .form-check-label .badge {
    background-color: #198754 !important;
}

#laboratory-tests-section.collapsing,
#laboratory-tests-section.collapse.show {
    transition: height 0.3s ease;
}

.lab-price-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#lab-chevron {
    transition: transform 0.3s ease;
}

#lab-chevron.rotated {
    transform: rotate(180deg);
}
</style>

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">New Transaction</h1>
        <p class="text-muted mb-0">Process patient billing for services rendered</p>
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>

<form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
    @csrf
    <div class="row">
        <!-- Patient Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-person me-2"></i>Patient Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="patient_search" class="form-label">Search Patient</label>
                        <div class="position-relative">
                            <div class="input-group">
                                <input type="text" class="form-control" id="patient_search" placeholder="Type patient name or ID..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <!-- Patient Dropdown -->
                            <div class="dropdown-menu w-100" id="patient_dropdown" style="display: none; max-height: 300px; overflow-y: auto;">
                                <div class="dropdown-header">Select Patient</div>
                                <div id="patient_results">
                                    <!-- Patient results will be populated here -->
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-item-text text-muted text-center" id="no_results" style="display: none;">
                                    <small>No patients found</small>
                                </div>
                                <div class="dropdown-item-text text-center" id="loading_results" style="display: none;">
                                    <small><i class="bi bi-hourglass-split me-1"></i>Searching...</small>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Start typing to search existing patients from EMR system</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="patient_name" class="form-label">Patient Name *</label>
                        <input type="text" class="form-control @error('patient_name') is-invalid @enderror" 
                               id="patient_name" name="patient_name" value="{{ old('patient_name') }}" required>
                        @error('patient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient ID</label>
                        <input type="text" class="form-control" id="patient_id" name="patient_id" 
                               value="{{ old('patient_id') }}" placeholder="Auto-generated if new">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Patient information will be auto-filled when selected from search</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Services Selection -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-clipboard-check me-2"></i>Select Services to Render
                        </h6>
                        <small class="badge bg-light text-dark" id="service-counter">0 selected</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Check the services that were rendered to the patient. Multiple services can be selected.</small>
                    </div>
                    
                    <div class="row">
                        @foreach($services as $service => $price)
                            @if($service === 'Laboratory' && $price === 'expandable')
                                <!-- Laboratory Tests - Expandable -->
                                <div class="col-12 mb-4">
                                    <div class="service-item border rounded position-relative" data-service="laboratory">
                                        <div class="p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center cursor-pointer" 
                                                 onclick="toggleLaboratoryTests()">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 service-name">
                                                        <i class="bi bi-flask me-2 text-info"></i>Laboratory Tests
                                                    </h6>
                                                    <small class="text-muted">Click to view and select specific laboratory tests</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="lab-price-badge badge bg-info">Click to expand</div>
                                                    <div class="ms-2">
                                                        <i class="bi bi-chevron-down" id="lab-chevron"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Expandable Laboratory Tests Section -->
                                        <div id="laboratory-tests-section" class="collapse">
                                            <div class="p-3 border-top">
                                                <div class="alert alert-info mb-3">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    <small><strong>Select specific laboratory tests:</strong> Check the tests the patient needs. Prices will be calculated automatically.</small>
                                                </div>
                                                
                                                @foreach($laboratoryTests as $category => $tests)
                                                <div class="mb-4">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="bi bi-{{ $category === 'Blood Tests' ? 'droplet' : 'clipboard-data' }} me-2"></i>
                                                        {{ $category }}
                                                    </h6>
                                                    <div class="row">
                                                        @foreach($tests as $testName => $testPrice)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="lab-test-item border rounded p-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input lab-test-checkbox" 
                                                                           type="checkbox" 
                                                                           name="services[]" 
                                                                           value="{{ $testName }}" 
                                                                           id="lab_{{ Str::slug($testName) }}" 
                                                                           data-price="{{ $testPrice }}"
                                                                           data-category="{{ $category }}">
                                                                    <label class="form-check-label w-100" for="lab_{{ Str::slug($testName) }}">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="flex-grow-1">
                                                                                <small class="fw-bold">{{ $testName }}</small>
                                                                            </div>
                                                                            <div>
                                                                                <small class="badge bg-secondary">₱{{ number_format($testPrice, 2) }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                                
                                                <!-- Laboratory Tests Summary -->
                                                <div class="lab-summary mt-3 p-3 bg-light rounded" id="lab-summary" style="display: none;">
                                                    <h6 class="text-success mb-2">
                                                        <i class="bi bi-check-circle me-2"></i>Selected Laboratory Tests
                                                    </h6>
                                                    <div id="selected-lab-tests"></div>
                                                    <hr class="my-2">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>Laboratory Total:</strong>
                                                        <strong id="lab-total" class="text-success">₱0.00</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($service === 'Pre-natal Package')
                                <!-- Pre-natal Package -->
                                <div class="col-12 mb-3">
                                    <div class="service-item border rounded p-3 position-relative" data-service="{{ $loop->index }}">
                                        <input class="form-check-input service-checkbox position-absolute top-0 end-0 m-2" 
                                               type="checkbox" name="services[]" value="{{ $service }}" 
                                               id="service_{{ $loop->index }}" data-price="{{ is_numeric($price) ? $price : '0' }}">
                                        <label class="form-check-label w-100 cursor-pointer" for="service_{{ $loop->index }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 service-name">{{ $service }}</h6>
                                                    <small class="text-muted">Comprehensive prenatal care package</small>
                                                    <div class="row mt-2">
                                                        <div class="col-md-6">
                                                            <small class="text-muted"><strong>Includes:</strong></small>
                                                            <ul class="list-unstyled ms-2 mb-0">
                                                                <li><small>Routine Check-ups</small></li>
                                                                <li><small>Blood and Urine Tests</small></li>
                                                                <li><small>Physical Assessments</small></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted">&nbsp;</small>
                                                            <ul class="list-unstyled ms-2 mb-0">
                                                                <li><small>Ultrasound Scans</small></li>
                                                                <li><small>Counselling and Education</small></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="service-price badge bg-secondary">₱{{ is_numeric($price) ? number_format($price, 2) : '0.00' }}</div>
                                                    <div class="service-status mt-1" style="display: none;">
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Selected
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @elseif($service === 'Pre-Employment/Annual Medical Exam')
                                <!-- Pre-Employment/Annual Medical Exam -->
                                <div class="col-12 mb-3">
                                    <div class="service-item border rounded p-3 position-relative" data-service="{{ $loop->index }}">
                                        <input class="form-check-input service-checkbox position-absolute top-0 end-0 m-2" 
                                               type="checkbox" name="services[]" value="{{ $service }}" 
                                               id="service_{{ $loop->index }}" data-price="{{ is_numeric($price) ? $price : '0' }}">
                                        <label class="form-check-label w-100 cursor-pointer" for="service_{{ $loop->index }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 service-name">{{ $service }}</h6>
                                                    <small class="text-muted">Complete medical screening for employment and annual health check-ups</small>
                                                    <div class="row mt-2">
                                                        <div class="col-md-4">
                                                            <small class="text-muted"><strong>Clinical Assessment:</strong></small>
                                                            <ul class="list-unstyled ms-2 mb-0">
                                                                <li><small>Physical examination</small></li>
                                                                <li><small>Medical history review</small></li>
                                                                <li><small>Psychological examination</small></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <small class="text-muted"><strong>Laboratory Tests:</strong></small>
                                                            <ul class="list-unstyled ms-2 mb-0">
                                                                <li><small>Complete Blood Count (CBC)</small></li>
                                                                <li><small>Urinalysis & Fecalysis</small></li>
                                                                <li><small>Drug & alcohol screening</small></li>
                                                                <li><small>Hepatitis B & HIV tests</small></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <small class="text-muted"><strong>Specialized Tests:</strong></small>
                                                            <ul class="list-unstyled ms-2 mb-0">
                                                                <li><small>Vision & Hearing tests</small></li>
                                                                <li><small>Chest X-ray</small></li>
                                                                <li><small>Electrocardiogram (ECG)</small></li>
                                                                <li><small>Pulmonary Function Test</small></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="service-price badge bg-secondary">₱{{ is_numeric($price) ? number_format($price, 2) : '0.00' }}</div>
                                                    <div class="service-status mt-1" style="display: none;">
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Selected
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <!-- Regular Services -->
                                <div class="col-md-6 mb-3">
                                    <div class="service-item border rounded p-3 position-relative" data-service="{{ $loop->index }}">
                                        <input class="form-check-input service-checkbox position-absolute top-0 end-0 m-2" 
                                               type="checkbox" name="services[]" value="{{ $service }}" 
                                               id="service_{{ $loop->index }}" data-price="{{ is_numeric($price) ? $price : '0' }}">
                                        <label class="form-check-label w-100 cursor-pointer" for="service_{{ $loop->index }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 service-name">{{ $service }}</h6>
                                                    <small class="text-muted">Click to select this service</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="service-price badge bg-secondary">₱{{ is_numeric($price) ? number_format($price, 2) : '0.00' }}</div>
                                                    <div class="service-status mt-1" style="display: none;">
                                                        <small class="text-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Selected
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    
                    @error('services')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                    
                    <!-- Active Services Summary -->
                    <div class="mt-4">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-list-check me-2"></i>Services Rendered Summary
                        </h6>
                        <div id="rendered-services-list" class="border rounded p-3 bg-light">
                            <div class="text-muted text-center py-2">
                                <i class="bi bi-clipboard me-2"></i>No services selected yet. Check services above to add them here.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Doctor Assignment -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-person-badge me-2"></i>Doctor Assignment
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="assigned_doctor" class="form-label">Assign Doctor *</label>
                                <select class="form-select" id="assigned_doctor" name="assigned_doctor" required>
                                    <option value="">Loading doctors...</option>
                                </select>
                                @error('assigned_doctor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="appointment_time" class="form-label">Appointment Time</label>
                                <input type="text" class="form-control" id="appointment_time" name="appointment_time" 
                                       placeholder="e.g., Today, 2:30 PM" value="Today, {{ date('g:i A') }}" readonly>
                                @error('appointment_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number</label>
                                <select class="form-select" id="room_number" name="room_number">
                                    <option value="">Select Room</option>
                                    <option value="Room 101">Room 101</option>
                                    <option value="Room 102">Room 102</option>
                                    <option value="Room 103">Room 103</option>
                                    <option value="Room 105">Room 105</option>
                                    <option value="Room 201">Room 201</option>
                                    <option value="Room 202">Room 202</option>
                                    <option value="Room 203">Room 203</option>
                                </select>
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>The assigned doctor information will appear on the receipt to guide the patient to the correct consultation room.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Billing Summary & Payment -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-credit-card me-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method *</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount_paid" class="form-label">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="amount_paid" name="amount_paid"
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Additional notes or special instructions...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Billing Summary -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-calculator me-2"></i>Billing Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div id="selected-services">
                        <p class="text-muted text-center">No services selected</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Medicine:</span>
                        <span id="medicine-total">₱0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total Amount:</strong>
                        <strong id="total-amount" class="text-primary">₱0.00</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3" id="change-section" style="display: none;">
                        <span>Change:</span>
                        <span id="change-amount" class="text-success">₱0.00</span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg" id="processBtn" disabled>
                            <i class="bi bi-check-circle me-2"></i>Process Payment
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="location.href='{{ route('pharmacy.sales') }}'">
                            <i class="bi bi-prescription2 me-2"></i>Add Medicines
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const selectedServicesDiv = document.getElementById('selected-services');
    const subtotalSpan = document.getElementById('subtotal');
    const totalAmountSpan = document.getElementById('total-amount');
    const processBtn = document.getElementById('processBtn');
    const amountPaidInput = document.getElementById('amount_paid');
    const changeSection = document.getElementById('change-section');
    const changeAmountSpan = document.getElementById('change-amount');
    
    let selectedServices = [];
    let subtotal = 0;
    
    // Load doctors from Auth service
    loadDoctors();
    
    function loadDoctors() {
        const doctorSelect = document.getElementById('assigned_doctor');
        
        fetch('http://127.0.0.1:8000/api/doctors')
            .then(response => response.json())
            .then(doctors => {
                doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                
                if (doctors.length === 0) {
                    doctorSelect.innerHTML += '<option value="" disabled>No doctors available</option>';
                } else {
                    doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.name;
                        option.textContent = `${doctor.name}${doctor.department ? ' - ' + doctor.department : ''}`;
                        doctorSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading doctors:', error);
                doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            });
    }
    
    // Service selection handler with visual feedback
    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateServiceVisuals(this);
            updateBillingSummary();
        });
    });
    
    // Amount paid handler
    amountPaidInput.addEventListener('input', function() {
        calculateChange();
    });
    
    function updateServiceVisuals(checkbox) {
        const serviceItem = checkbox.closest('.service-item');
        const serviceStatus = serviceItem.querySelector('.service-status');
        const servicePriceBadge = serviceItem.querySelector('.service-price');
        
        if (checkbox.checked) {
            // Visual feedback for selected service
            serviceItem.classList.add('border-success', 'bg-light');
            serviceItem.classList.remove('border-secondary');
            servicePriceBadge.classList.remove('bg-secondary');
            servicePriceBadge.classList.add('bg-success');
            serviceStatus.style.display = 'block';
        } else {
            // Reset visual feedback for unselected service
            serviceItem.classList.remove('border-success', 'bg-light');
            serviceItem.classList.add('border-secondary');
            servicePriceBadge.classList.remove('bg-success');
            servicePriceBadge.classList.add('bg-secondary');
            serviceStatus.style.display = 'none';
        }
    }

    function addService(serviceName, servicePrice) {
        // Check if service already exists
        const existingIndex = selectedServices.findIndex(s => s.name === serviceName);
        if (existingIndex === -1) {
            selectedServices.push({
                name: serviceName,
                price: servicePrice
            });
            subtotal += servicePrice;
        }
    }

    function updateBillingSummary() {
        selectedServices = [];
        subtotal = 0;
        
        // Add regular services
        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                addService(checkbox.value, parseFloat(checkbox.dataset.price));
            }
        });
        
        // Add laboratory tests to subtotal
        const checkedLabTests = document.querySelectorAll('.lab-test-checkbox:checked');
        checkedLabTests.forEach(checkbox => {
            subtotal += parseFloat(checkbox.dataset.price);
        });
        
        // Count total services (regular + lab tests)
        const totalServicesCount = selectedServices.length + checkedLabTests.length;
        
        // Update service counter badge
        const serviceCounter = document.getElementById('service-counter');
        serviceCounter.textContent = `${totalServicesCount} selected`;
        
        // Update rendered services list
        const renderedServicesList = document.getElementById('rendered-services-list');
        if (totalServicesCount > 0) {
            let renderedHtml = '<div class="row">';
            
            // Add regular services
            selectedServices.forEach((service, index) => {
                renderedHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center p-2 bg-success text-white rounded">
                            <div>
                                <small class="fw-bold">${service.name}</small>
                            </div>
                            <div>
                                <small>₱${service.price.toFixed(2)}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            // Add laboratory tests
            checkedLabTests.forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                renderedHtml += `
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center p-2 bg-info text-white rounded">
                            <div>
                                <small class="fw-bold">
                                    <i class="bi bi-flask me-1"></i>${checkbox.value}
                                </small>
                            </div>
                            <div>
                                <small>₱${price.toFixed(2)}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            renderedHtml += '</div>';
            renderedServicesList.innerHTML = renderedHtml;
        } else {
            renderedServicesList.innerHTML = `
                <div class="text-muted text-center py-2">
                    <i class="bi bi-clipboard me-2"></i>No services selected yet. Check services above to add them here.
                </div>
            `;
        }
        
        // Update selected services display in billing summary
        if (totalServicesCount > 0) {
            let servicesHtml = '';
            
            // Add regular services
            selectedServices.forEach(service => {
                servicesHtml += `
                    <div class="d-flex justify-content-between mb-1">
                        <small>${service.name}</small>
                        <small>₱${service.price.toFixed(2)}</small>
                    </div>
                `;
            });
            
            // Add laboratory tests
            checkedLabTests.forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                servicesHtml += `
                    <div class="d-flex justify-content-between mb-1">
                        <small><i class="bi bi-flask me-1"></i>${checkbox.value}</small>
                        <small>₱${price.toFixed(2)}</small>
                    </div>
                `;
            });
            
            selectedServicesDiv.innerHTML = servicesHtml;
            processBtn.disabled = false;
        } else {
            selectedServicesDiv.innerHTML = '<p class="text-muted text-center mb-0">No services selected</p>';
            processBtn.disabled = true;
        }
        
        // Update totals
        subtotalSpan.textContent = `₱${subtotal.toFixed(2)}`;
        totalAmountSpan.textContent = `₱${subtotal.toFixed(2)}`;
        
        // Set amount paid to total for quick processing
        amountPaidInput.value = subtotal.toFixed(2);
        calculateChange();
    }
    
    function calculateChange() {
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        const total = subtotal;
        
        if (amountPaid >= total && total > 0) {
            const change = amountPaid - total;
            changeAmountSpan.textContent = `₱${change.toFixed(2)}`;
            changeSection.style.display = change > 0 ? 'flex' : 'none';
        } else {
            changeSection.style.display = 'none';
        }
    }
    
    // Patient search functionality with dropdown
    const patientSearch = document.getElementById('patient_search');
    const patientName = document.getElementById('patient_name');
    const patientId = document.getElementById('patient_id');
    const patientDropdown = document.getElementById('patient_dropdown');
    const patientResults = document.getElementById('patient_results');
    const noResults = document.getElementById('no_results');
    const loadingResults = document.getElementById('loading_results');
    
    let searchTimeout;
    
    patientSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideDropdown();
            return;
        }
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Show loading
        showLoading();
        
        // Debounce search
        searchTimeout = setTimeout(() => {
            searchPatients(query);
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.position-relative')) {
            hideDropdown();
        }
    });
    
    function searchPatients(query) {
        fetch(`/patients/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(patients => {
                hideLoading();
                displayPatients(patients);
            })
            .catch(error => {
                console.error('Error searching patients:', error);
                hideLoading();
                showNoResults();
            });
    }
    
    function displayPatients(patients) {
        patientResults.innerHTML = '';
        
        if (patients.length === 0) {
            showNoResults();
            return;
        }
        
        patients.forEach(patient => {
            const patientItem = document.createElement('div');
            patientItem.className = 'dropdown-item patient-item';
            patientItem.style.cursor = 'pointer';
            patientItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${patient.full_name}</strong>
                        <br><small class="text-muted">ID: ${patient.patient_code}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">${patient.age} yrs, ${patient.gender}</small>
                        <br><small class="text-muted">${patient.phone}</small>
                    </div>
                </div>
            `;
            
            patientItem.addEventListener('click', function() {
                selectPatient(patient);
            });
            
            patientResults.appendChild(patientItem);
        });
        
        showDropdown();
        hideNoResults();
    }
    
    function selectPatient(patient) {
        patientSearch.value = patient.full_name;
        patientName.value = patient.full_name;
        patientId.value = patient.id; // Use the actual ID
        hideDropdown();
    }
    
    function showDropdown() {
        patientDropdown.style.display = 'block';
        patientDropdown.classList.add('show');
    }
    
    function hideDropdown() {
        patientDropdown.style.display = 'none';
        patientDropdown.classList.remove('show');
    }
    
    function showLoading() {
        loadingResults.style.display = 'block';
        noResults.style.display = 'none';
        showDropdown();
    }
    
    function hideLoading() {
        loadingResults.style.display = 'none';
    }
    
    function showNoResults() {
        noResults.style.display = 'block';
        showDropdown();
    }
    
    function hideNoResults() {
        noResults.style.display = 'none';
    }
    
    // Laboratory Tests Functionality
    window.toggleLaboratoryTests = function() {
        const labSection = document.getElementById('laboratory-tests-section');
        const chevron = document.getElementById('lab-chevron');
        
        if (labSection.classList.contains('show')) {
            labSection.classList.remove('show');
            chevron.classList.remove('rotated');
        } else {
            labSection.classList.add('show');
            chevron.classList.add('rotated');
        }
    };

    // Laboratory test checkboxes handler
    const labTestCheckboxes = document.querySelectorAll('.lab-test-checkbox');
    labTestCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateLabTestVisuals(this);
            updateLabSummary();
            updateBillingSummary(); // Update main billing summary
        });
    });

    function updateLabTestVisuals(checkbox) {
        const labTestItem = checkbox.closest('.lab-test-item');
        
        if (checkbox.checked) {
            labTestItem.classList.add('border-primary', 'bg-light');
        } else {
            labTestItem.classList.remove('border-primary', 'bg-light');
        }
    }

    function updateLabSummary() {
        const labSummary = document.getElementById('lab-summary');
        const selectedLabTests = document.getElementById('selected-lab-tests');
        const labTotal = document.getElementById('lab-total');
        const labPriceBadge = document.querySelector('.lab-price-badge');
        
        const checkedLabTests = document.querySelectorAll('.lab-test-checkbox:checked');
        
        if (checkedLabTests.length > 0) {
            let labTotalPrice = 0;
            let labTestsHtml = '';
            
            checkedLabTests.forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                const category = checkbox.dataset.category;
                labTotalPrice += price;
                
                labTestsHtml += `
                    <div class="d-flex justify-content-between mb-1">
                        <small><i class="bi bi-check-circle-fill text-success me-1"></i>${checkbox.value}</small>
                        <small>₱${price.toFixed(2)}</small>
                    </div>
                `;
            });
            
            selectedLabTests.innerHTML = labTestsHtml;
            labTotal.textContent = `₱${labTotalPrice.toFixed(2)}`;
            labPriceBadge.textContent = `₱${labTotalPrice.toFixed(2)} (${checkedLabTests.length} tests)`;
            labPriceBadge.classList.remove('bg-info');
            labPriceBadge.classList.add('bg-success');
            labSummary.style.display = 'block';
        } else {
            labSummary.style.display = 'none';
            labPriceBadge.textContent = 'Click to expand';
            labPriceBadge.classList.remove('bg-success');
            labPriceBadge.classList.add('bg-info');
        }
    }

    // Form submission with SweetAlert2 confirmation
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default to handle with SweetAlert2
        
        const hasRegularServices = selectedServices.length > 0;
        const hasLabTests = document.querySelectorAll('.lab-test-checkbox:checked').length > 0;
        
        if (!hasRegularServices && !hasLabTests) {
            Swal.fire({
                icon: 'warning',
                title: 'No Services Selected',
                text: 'Please select at least one service or laboratory test.',
                confirmButtonColor: '#28a745'
            });
            return;
        }
        
        const patientNameValue = patientName.value || 'Walk-in Customer';
        const totalAmount = subtotal.toFixed(2);
        
        // Build comprehensive service list including lab tests
        let allServices = [...selectedServices];
        document.querySelectorAll('.lab-test-checkbox:checked').forEach(checkbox => {
            allServices.push({
                name: checkbox.value,
                price: parseFloat(checkbox.dataset.price)
            });
        });
        
        Swal.fire({
            title: 'Process Payment',
            html: `
                <div class="text-start">
                    <p><strong>Patient:</strong> ${patientNameValue}</p>
                    <p><strong>Total Amount:</strong> ₱${totalAmount}</p>
                    <p><strong>Services:</strong></p>
                    <ul class="list-unstyled ms-3">
                        ${allServices.map(service => `<li>• ${service.name} - ₱${service.price.toFixed(2)}</li>`).join('')}
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Process Payment',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable submit button immediately to prevent double submission
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                
                // Show processing alert
                Swal.fire({
                    title: 'Processing Payment...',
                    text: 'Please wait while we process the transaction.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the form after a short delay to show processing
                setTimeout(() => {
                    const form = document.getElementById('transactionForm');
                    // Check if form is already being submitted
                    if (form.dataset.submitting === 'true') {
                        return;
                    }
                    // Mark form as submitting
                    form.dataset.submitting = 'true';
                    // Submit the form normally - checkboxes will handle the data
                    form.submit();
                }, 1500);
            }
        });
    });
    
    // Real-time appointment time updater
    function updateAppointmentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        const appointmentTimeField = document.getElementById('appointment_time');
        if (appointmentTimeField) {
            appointmentTimeField.value = `Today, ${timeString}`;
        }
    }
    
    // Update appointment time immediately
    updateAppointmentTime();
    
    // Update appointment time every 30 seconds (less resource intensive than every second)
    setInterval(updateAppointmentTime, 30000);
});
</script>
@endsection
