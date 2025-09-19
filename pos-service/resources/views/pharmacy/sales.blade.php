@extends('layouts.app')

@section('title', 'Medicine Sales - POS System')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Medicine Sales</h1>
        <p class="text-muted mb-0">Sell prescribed medicines to patients</p>
    </div>
    <div>
        <button class="btn btn-info me-2" onclick="location.href='{{ route('pharmacy.search') }}'">
            <i class="bi bi-search me-1"></i>Search Medicines
        </button>
        <button class="btn btn-outline-secondary" onclick="location.href='{{ route('pharmacy.history') }}'">
            <i class="bi bi-clock-history me-1"></i>Sales History
        </button>
    </div>
</div>

<form id="pharmacySaleForm" action="{{ route('pharmacy.sell') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Patient & Cart -->
        <div class="col-lg-4">
            <!-- Patient Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
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
                               value="{{ old('patient_id') }}" placeholder="Auto-filled from search">
                    </div>
                    
                    <div class="mb-3">
                        <label for="prescription_note" class="form-label">Prescription Note</label>
                        <textarea class="form-control" id="prescription_note" rows="2" 
                                  placeholder="Doctor's prescription details..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Patient information will be auto-filled when selected from search</small>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-cart me-2"></i>Medicine Cart
                    </h6>
                </div>
                <div class="card-body">
                    <div id="cart-items">
                        <p class="text-muted text-center">No medicines added</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">₱0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="cart-total" class="text-success">₱0.00</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method *</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg" id="processBtn" disabled>
                            <i class="bi bi-check-circle me-2"></i>Process Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Medicine Inventory -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 font-weight-bold">
                                <i class="bi bi-prescription2 me-2"></i>Available Medicines
                            </h6>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="medicine-search" 
                                       placeholder="Search medicines...">
                                <button class="btn btn-outline-light btn-sm" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0" id="medicine-grid">
                        @foreach($medicines as $medicine)
                        <div class="col-md-6 medicine-item" data-name="{{ strtolower($medicine->name) }}" 
                             data-category="{{ strtolower($medicine->category) }}">
                            <div class="border-bottom p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="medicine-info">
                                            <h6 class="mb-1">{{ $medicine->name }}</h6>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark me-2">{{ $medicine->dosage }}</span>
                                                <span class="badge bg-info">{{ $medicine->category }}</span>
                                            </div>
                                            <div class="mt-2">
                                                <strong class="text-success">₱{{ number_format($medicine->price, 2) }}</strong>
                                                <span class="text-muted ms-2">
                                                    Stock: 
                                                    <span class="stock-count {{ $medicine->stock < 10 ? 'text-danger' : 'text-success' }}">
                                                        {{ $medicine->stock }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="quantity-controls mb-2">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary qty-minus" type="button" 
                                                        data-medicine-id="{{ $medicine->id }}">-</button>
                                                <input type="number" class="form-control text-center qty-input" 
                                                       value="0" min="0" max="{{ $medicine->stock }}" 
                                                       data-medicine-id="{{ $medicine->id }}">
                                                <button class="btn btn-outline-secondary qty-plus" type="button" 
                                                        data-medicine-id="{{ $medicine->id }}">+</button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success add-to-cart" 
                                                data-medicine='@json($medicine)' 
                                                {{ $medicine->stock == 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
    let cart = [];
    let cartTotal = 0;
    
    const cartItemsDiv = document.getElementById('cart-items');
    const cartSubtotalSpan = document.getElementById('cart-subtotal');
    const cartTotalSpan = document.getElementById('cart-total');
    const processBtn = document.getElementById('processBtn');
    
    // Patient search functionality (copied from New Transaction)
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
        patientSearch.value = patient.name;
        patientName.value = patient.name;
        patientId.value = patient.patient_id;
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
    
    // Medicine search functionality
    const medicineSearch = document.getElementById('medicine-search');
    medicineSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const medicineItems = document.querySelectorAll('.medicine-item');
        
        medicineItems.forEach(item => {
            const name = item.dataset.name;
            const category = item.dataset.category;
            
            if (name.includes(query) || category.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Quantity controls
    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const medicineId = this.dataset.medicineId;
            const input = document.querySelector(`input[data-medicine-id="${medicineId}"]`);
            const currentValue = parseInt(input.value);
            if (currentValue > 0) {
                input.value = currentValue - 1;
            }
        });
    });
    
    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const medicineId = this.dataset.medicineId;
            const input = document.querySelector(`input[data-medicine-id="${medicineId}"]`);
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.max);
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        });
    });
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const medicine = JSON.parse(this.dataset.medicine);
            const qtyInput = document.querySelector(`input[data-medicine-id="${medicine.id}"]`);
            const quantity = parseInt(qtyInput.value);
            
            if (quantity > 0) {
                addToCart(medicine, quantity);
                qtyInput.value = 0; // Reset quantity
            } else {
                Swal.fire({
                    title: 'No Quantity Selected',
                    text: 'Please select a quantity before adding to cart.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        popup: 'border-0 shadow-lg',
                        title: 'text-info fw-bold'
                    }
                });
            }
        });
    });
    
    function addToCart(medicine, quantity) {
        const existingItem = cart.find(item => item.id === medicine.id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
            existingItem.total = existingItem.quantity * existingItem.price;
        } else {
            cart.push({
                id: medicine.id,
                name: medicine.name,
                dosage: medicine.dosage,
                price: medicine.price,
                quantity: quantity,
                total: quantity * medicine.price
            });
        }
        
        updateCartDisplay();
    }
    
    function removeFromCart(medicineId) {
        cart = cart.filter(item => item.id !== medicineId);
        updateCartDisplay();
    }
    
    function updateCartDisplay() {
        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<p class="text-muted text-center mb-0">No medicines added</p>';
            processBtn.disabled = true;
        } else {
            let cartHtml = '';
            cartTotal = 0;
            
            cart.forEach(item => {
                cartTotal += item.total;
                cartHtml += `
                    <div class="cart-item mb-2 p-2 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <strong>${item.name}</strong>
                                <small class="text-muted d-block">${item.dosage} × ${item.quantity}</small>
                            </div>
                            <div class="text-end">
                                <div>₱${item.total.toFixed(2)}</div>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" 
                                        data-medicine-id="${item.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="medicines[${item.id}][id]" value="${item.id}">
                        <input type="hidden" name="medicines[${item.id}][quantity]" value="${item.quantity}">
                    </div>
                `;
            });
            
            cartItemsDiv.innerHTML = cartHtml;
            processBtn.disabled = false;
            
            // Add remove functionality to new buttons
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    removeFromCart(this.dataset.medicineId);
                });
            });
        }
        
        cartSubtotalSpan.textContent = `₱${cartTotal.toFixed(2)}`;
        cartTotalSpan.textContent = `₱${cartTotal.toFixed(2)}`;
    }
    
    // Form submission
    document.getElementById('pharmacySaleForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            Swal.fire({
                title: 'Empty Cart',
                text: 'Please add medicines to cart before processing the sale.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                customClass: {
                    popup: 'border-0 shadow-lg',
                    title: 'text-warning fw-bold'
                }
            });
            return;
        }
        
        const patientNameValue = patientName.value;
        const paymentMethod = document.getElementById('payment_method').value;
        
        if (!patientNameValue || !paymentMethod) {
            e.preventDefault();
            Swal.fire({
                title: 'Missing Information',
                text: 'Please fill in all required fields before processing the sale.',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                customClass: {
                    popup: 'border-0 shadow-lg',
                    title: 'text-warning fw-bold'
                }
            });
            return;
        }
        
        e.preventDefault(); // Prevent default submission
        
        Swal.fire({
            title: 'Confirm Medicine Sale',
            html: `
                <div class="text-start">
                    <p><strong>Patient:</strong> ${patientNameValue}</p>
                    <p><strong>Total Amount:</strong> <span class="text-success fs-5">₱${cartTotal.toFixed(2)}</span></p>
                    <p><strong>Payment Method:</strong> ${paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1)}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle me-1"></i>Process Sale',
            cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Cancel',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            customClass: {
                popup: 'border-0 shadow-lg',
                title: 'text-primary fw-bold',
                htmlContainer: 'text-muted'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing animation
                Swal.fire({
                    title: 'Processing Sale...',
                    html: '<div class="spinner-border text-primary" role="status"></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'border-0 shadow-lg'
                    }
                });
                
                // Submit the form after a brief delay for better UX
                setTimeout(() => {
                    document.getElementById('pharmacySaleForm').submit();
                }, 500);
            }
        });
    });
});
</script>
@endsection