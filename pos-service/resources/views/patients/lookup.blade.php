@extends('layouts.app')

@section('title', 'Patient Lookup - POS System')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Patient Lookup</h1>
        <p class="text-muted mb-0">Search and view patient information from EMR system</p>
    </div>
    <div>
        <button class="btn btn-success" onclick="location.href='{{ route('transactions.create') }}'">
            <i class="bi bi-plus-circle me-1"></i>New Transaction
        </button>
    </div>
</div>

<!-- Search Interface -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-search me-2"></i>Search Patients
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" id="patient_search" 
                               placeholder="Type patient name or ID..." autocomplete="off">
                        <button class="btn btn-primary" type="button" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <!-- Patient Dropdown -->
                    <div class="dropdown-menu w-100" id="patient_dropdown" style="display: none; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header">Select Patient</div>
                        <div id="patient_results">
                            <!-- Patient results will be populated here -->
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item-text text-muted text-center" id="no_results" style="display: none;">
                            <small>No patients found</small>
                        </div>
                        <div class="dropdown-item-text text-center" id="loading_results" style="display: none;">
                            <small><i class="bi bi-hourglass-split me-1"></i>Searching EMR system...</small>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Search patients registered in the EMR system</small>
            </div>
            <div class="col-lg-6">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Quick Actions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Type patient name or ID to search</li>
                        <li>Click on a patient to view details</li>
                        <li>Use "New Transaction" to process billing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selected Patient Details -->
<div class="card shadow" id="patient_details" style="display: none;">
    <div class="card-header bg-success text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-person me-2"></i>Patient Details
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="patient-info">
                    <h5 id="selected_patient_name">-</h5>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Patient ID:</strong></div>
                        <div class="col-sm-8" id="selected_patient_id">-</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Age:</strong></div>
                        <div class="col-sm-8" id="selected_patient_age">-</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Sex:</strong></div>
                        <div class="col-sm-8" id="selected_patient_sex">-</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Contact:</strong></div>
                        <div class="col-sm-8" id="selected_patient_contact">-</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" id="create_transaction_btn">
                        <i class="bi bi-cash-coin me-2"></i>Create Transaction
                    </button>
                    <button class="btn btn-success" id="medicine_sale_btn">
                        <i class="bi bi-capsule me-2"></i>Medicine Sale
                    </button>
                    <button class="btn btn-info" id="view_history_btn">
                        <i class="bi bi-clock-history me-2"></i>View Transaction History
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Searches -->
<div class="card shadow mt-4">
    <div class="card-header bg-light">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-clock-history me-2"></i>Recent Patient Searches
        </h6>
    </div>
    <div class="card-body">
        <div id="recent_searches">
            <p class="text-muted text-center">No recent searches</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientSearch = document.getElementById('patient_search');
    const patientDropdown = document.getElementById('patient_dropdown');
    const patientResults = document.getElementById('patient_results');
    const noResults = document.getElementById('no_results');
    const loadingResults = document.getElementById('loading_results');
    const patientDetails = document.getElementById('patient_details');
    
    let searchTimeout;
    let selectedPatient = null;
    
    patientSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideDropdown();
            return;
        }
        
        clearTimeout(searchTimeout);
        showLoading();
        
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
                        <strong>${patient.name}</strong>
                        <br><small class="text-muted">ID: ${patient.patient_id}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">${patient.age} yrs, ${patient.sex}</small>
                        <br><small class="text-muted">${patient.contact}</small>
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
        selectedPatient = patient;
        patientSearch.value = patient.name;
        hideDropdown();
        showPatientDetails(patient);
        addToRecentSearches(patient);
    }
    
    function showPatientDetails(patient) {
        document.getElementById('selected_patient_name').textContent = patient.name;
        document.getElementById('selected_patient_id').textContent = patient.patient_id;
        document.getElementById('selected_patient_age').textContent = patient.age + ' years';
        document.getElementById('selected_patient_sex').textContent = patient.sex;
        document.getElementById('selected_patient_contact').textContent = patient.contact;
        
        patientDetails.style.display = 'block';
        
        // Setup action buttons
        document.getElementById('create_transaction_btn').onclick = function() {
            window.location.href = `{{ route('transactions.create') }}?patient_id=${patient.patient_id}`;
        };
        
        document.getElementById('medicine_sale_btn').onclick = function() {
            window.location.href = `{{ route('pharmacy.sales') }}?patient_id=${patient.patient_id}`;
        };
        
        document.getElementById('view_history_btn').onclick = function() {
            alert('Transaction history feature coming soon!');
        };
    }
    
    function addToRecentSearches(patient) {
        // Simple recent searches implementation
        let recentSearches = JSON.parse(localStorage.getItem('recentPatientSearches') || '[]');
        
        // Remove if already exists
        recentSearches = recentSearches.filter(p => p.patient_id !== patient.patient_id);
        
        // Add to beginning
        recentSearches.unshift(patient);
        
        // Keep only last 5
        recentSearches = recentSearches.slice(0, 5);
        
        localStorage.setItem('recentPatientSearches', JSON.stringify(recentSearches));
        displayRecentSearches();
    }
    
    function displayRecentSearches() {
        const recentSearches = JSON.parse(localStorage.getItem('recentPatientSearches') || '[]');
        const container = document.getElementById('recent_searches');
        
        if (recentSearches.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No recent searches</p>';
            return;
        }
        
        let html = '<div class="row">';
        recentSearches.forEach(patient => {
            html += `
                <div class="col-md-6 mb-2">
                    <div class="card border-left-primary h-100">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${patient.name}</strong>
                                    <br><small class="text-muted">ID: ${patient.patient_id}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" onclick="selectRecentPatient('${patient.patient_id}')">
                                    Select
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.innerHTML = html;
    }
    
    // Global function for recent patient selection
    window.selectRecentPatient = function(patientId) {
        const recentSearches = JSON.parse(localStorage.getItem('recentPatientSearches') || '[]');
        const patient = recentSearches.find(p => p.patient_id === patientId);
        if (patient) {
            selectPatient(patient);
        }
    };
    
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
    
    // Load recent searches on page load
    displayRecentSearches();
});
</script>
@endsection
