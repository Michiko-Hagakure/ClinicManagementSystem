@extends('layouts.app')

@section('title', 'Add Medicine - Mary Angels Diagnostic Clinic')
@section('page-title', 'Add New Medicine')
@section('page-description', 'Add a new medicine to the inventory')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Medicine Information
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('medicine.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., Paracetamol"
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dosage" class="form-label">Dosage <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('dosage') is-invalid @enderror" 
                                   id="dosage" 
                                   name="dosage" 
                                   value="{{ old('dosage') }}" 
                                   placeholder="e.g., 500mg, 10ml"
                                   required>
                            @error('dosage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category" 
                                    required>
                                <option value="">Select Category</option>
                                <option value="Capsules/Tablets" {{ old('category') == 'Capsules/Tablets' ? 'selected' : '' }}>Capsules/Tablets</option>
                                <option value="Antibiotics (TABS/CAPS)" {{ old('category') == 'Antibiotics (TABS/CAPS)' ? 'selected' : '' }}>Antibiotics (TABS/CAPS)</option>
                                <option value="Suspension" {{ old('category') == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                <option value="Drops/Ointment/Cream/Nebule" {{ old('category') == 'Drops/Ointment/Cream/Nebule' ? 'selected' : '' }}>Drops/Ointment/Cream/Nebule</option>
                                <option value="IV Meds (VIAL/AMPULE)" {{ old('category') == 'IV Meds (VIAL/AMPULE)' ? 'selected' : '' }}>IV Meds (VIAL/AMPULE)</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label">Initial Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   id="stock_quantity" 
                                   name="stock_quantity" 
                                   value="{{ old('stock_quantity', 0) }}" 
                                   min="0" 
                                   step="1"
                                   required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of units available</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', 0) }}" 
                                   min="0" 
                                   step="0.01"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Price per unit</small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> If initial stock is 10 or below, a low stock alert will be automatically created.
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('medicine.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Add Medicine
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format price input on blur
    const priceInput = document.getElementById('price');
    if (priceInput) {
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
    
    // Ensure stock quantity is whole number
    const stockInput = document.getElementById('stock_quantity');
    if (stockInput) {
        stockInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = Math.round(parseFloat(this.value));
            }
        });
    }
});
</script>
@endsection

