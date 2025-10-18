@extends('layouts.app')

@section('title', 'Edit Medicine - Mary Angels Diagnostic Clinic')
@section('page-title', 'Edit Medicine')
@section('page-description', 'Update medicine information')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil me-2"></i>Edit Medicine: {{ $medicine->name }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('medicine.update', $medicine) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $medicine->name) }}" 
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
                                   value="{{ old('dosage', $medicine->dosage) }}" 
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
                                <option value="Capsules/Tablets" {{ old('category', $medicine->category) == 'Capsules/Tablets' ? 'selected' : '' }}>Capsules/Tablets</option>
                                <option value="Antibiotics (TABS/CAPS)" {{ old('category', $medicine->category) == 'Antibiotics (TABS/CAPS)' ? 'selected' : '' }}>Antibiotics (TABS/CAPS)</option>
                                <option value="Suspension" {{ old('category', $medicine->category) == 'Suspension' ? 'selected' : '' }}>Suspension</option>
                                <option value="Drops/Ointment/Cream/Nebule" {{ old('category', $medicine->category) == 'Drops/Ointment/Cream/Nebule' ? 'selected' : '' }}>Drops/Ointment/Cream/Nebule</option>
                                <option value="IV Meds (VIAL/AMPULE)" {{ old('category', $medicine->category) == 'IV Meds (VIAL/AMPULE)' ? 'selected' : '' }}>IV Meds (VIAL/AMPULE)</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="current_stock" class="form-label">Current Stock Quantity</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="current_stock" 
                                   value="{{ $medicine->stock_quantity }}" 
                                   readonly 
                                   disabled>
                            <small class="text-muted">To update stock, use the "Add Stock" button on the inventory page</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $medicine->price) }}" 
                                   min="0" 
                                   step="0.01"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Price per unit</small>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> Stock quantity cannot be edited here. Use the "Add Stock" feature on the inventory page to modify stock levels.
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('medicine.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Medicine
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
});
</script>
@endsection

