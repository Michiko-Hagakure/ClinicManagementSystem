@extends('layouts.app')

@section('title', 'Point of Sale - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Point of Sale</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Process New Sale</small>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>Print Receipt
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Product Selection -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-grid me-2"></i>Product Selection
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Search products by name or barcode...">
                        </div>
                    </div>

                    <!-- Product Categories -->
                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active">All</button>
                            <button type="button" class="btn btn-outline-primary">Medicines</button>
                            <button type="button" class="btn btn-outline-primary">Vitamins</button>
                            <button type="button" class="btn btn-outline-primary">Supplies</button>
                            <button type="button" class="btn btn-outline-primary">Equipment</button>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-capsule-pill fa-3x text-primary mb-2"></i>
                                    <h6 class="card-title">Paracetamol 500mg</h6>
                                    <p class="card-text text-muted small">30 tablets per box</p>
                                    <p class="text-success font-weight-bold">₱30.00</p>
                                    <span class="badge bg-success">48 in stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-heart-pulse fa-3x text-success mb-2"></i>
                                    <h6 class="card-title">Vitamin C 1000mg</h6>
                                    <p class="card-text text-muted small">100 tablets per bottle</p>
                                    <p class="text-success font-weight-bold">₱150.00</p>
                                    <span class="badge bg-warning">5 in stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-droplet fa-3x text-info mb-2"></i>
                                    <h6 class="card-title">Alcohol 70%</h6>
                                    <p class="card-text text-muted small">500ml bottle</p>
                                    <p class="text-success font-weight-bold">₱40.00</p>
                                    <span class="badge bg-success">22 in stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check fa-3x text-warning mb-2"></i>
                                    <h6 class="card-title">Face Mask (Box)</h6>
                                    <p class="card-text text-muted small">50 pieces per box</p>
                                    <p class="text-success font-weight-bold">₱200.00</p>
                                    <span class="badge bg-danger">2 in stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-bandaid fa-3x text-secondary mb-2"></i>
                                    <h6 class="card-title">Betadine Solution</h6>
                                    <p class="card-text text-muted small">60ml bottle</p>
                                    <p class="text-success font-weight-bold">₱80.00</p>
                                    <span class="badge bg-success">18 in stock</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card product-card h-100" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <i class="bi bi-thermometer fa-3x text-danger mb-2"></i>
                                    <h6 class="card-title">Digital Thermometer</h6>
                                    <p class="card-text text-muted small">Electronic thermometer</p>
                                    <p class="text-success font-weight-bold">₱350.00</p>
                                    <span class="badge bg-warning">3 in stock</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shopping Cart -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-cart me-2"></i>Shopping Cart
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Customer Info -->
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <div class="input-group">
                            <select class="form-select">
                                <option>Walk-in Customer</option>
                                <option>Maria Santos</option>
                                <option>Juan Dela Cruz</option>
                                <option>Ana Rodriguez</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="cart-items mb-3" style="max-height: 300px; overflow-y: auto;">
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Paracetamol 500mg</h6>
                                    <small class="text-muted">₱30.00 each</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="input-group input-group-sm" style="width: 100px;">
                                    <button class="btn btn-outline-secondary" type="button">-</button>
                                    <input type="number" class="form-control text-center" value="2">
                                    <button class="btn btn-outline-secondary" type="button">+</button>
                                </div>
                                <strong>₱60.00</strong>
                            </div>
                        </div>

                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Vitamin C 1000mg</h6>
                                    <small class="text-muted">₱150.00 each</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="input-group input-group-sm" style="width: 100px;">
                                    <button class="btn btn-outline-secondary" type="button">-</button>
                                    <input type="number" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button">+</button>
                                </div>
                                <strong>₱150.00</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>₱210.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tax (0%):</span>
                            <span>₱0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Discount:</span>
                            <span>-₱0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-success">₱210.00</strong>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="mt-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select mb-2">
                            <option>Cash</option>
                            <option>Credit Card</option>
                            <option>GCash</option>
                            <option>PayMaya</option>
                        </select>

                        <label class="form-label">Amount Received</label>
                        <input type="number" class="form-control mb-2" placeholder="0.00" step="0.01">

                        <div class="d-flex justify-content-between text-muted">
                            <span>Change:</span>
                            <span>₱0.00</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-grid gap-2">
                        <button class="btn btn-success btn-lg">
                            <i class="bi bi-credit-card me-2"></i>Process Payment
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e3e6f0;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #4e73df;
}

.fa-3x {
    font-size: 3em;
}

.cart-items::-webkit-scrollbar {
    width: 6px;
}

.cart-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.cart-items::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.cart-items::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
