@extends('layouts.app')

@section('title', 'Products & Inventory - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Products & Inventory</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Manage Products and Stock</small>
        </div>
        <div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add New Product
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>All Categories</option>
                        <option>Medicines</option>
                        <option>Vitamins</option>
                        <option>Supplies</option>
                        <option>Equipment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>All Stock Levels</option>
                        <option>In Stock</option>
                        <option>Low Stock</option>
                        <option>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="bi bi-grid me-2"></i>Product Inventory
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock Qty</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>MED001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-capsule-pill text-primary me-2"></i>
                                    <div>
                                        <strong>Paracetamol 500mg</strong>
                                        <br><small class="text-muted">30 tablets per box</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info">Medicine</span></td>
                            <td>₱30.00</td>
                            <td>48</td>
                            <td><span class="badge bg-success">In Stock</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 1) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 1) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>VIT001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-heart-pulse text-success me-2"></i>
                                    <div>
                                        <strong>Vitamin C 1000mg</strong>
                                        <br><small class="text-muted">100 tablets per bottle</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-success">Vitamin</span></td>
                            <td>₱150.00</td>
                            <td>5</td>
                            <td><span class="badge bg-warning">Low Stock</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 2) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 2) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>SUP001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-droplet text-info me-2"></i>
                                    <div>
                                        <strong>Alcohol 70%</strong>
                                        <br><small class="text-muted">500ml bottle</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">Supply</span></td>
                            <td>₱40.00</td>
                            <td>22</td>
                            <td><span class="badge bg-success">In Stock</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 3) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 3) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>SUP002</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check text-warning me-2"></i>
                                    <div>
                                        <strong>Face Mask (Box)</strong>
                                        <br><small class="text-muted">50 pieces per box</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">Supply</span></td>
                            <td>₱200.00</td>
                            <td>2</td>
                            <td><span class="badge bg-danger">Critical</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 4) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 4) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>MED002</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bandaid text-secondary me-2"></i>
                                    <div>
                                        <strong>Betadine Solution</strong>
                                        <br><small class="text-muted">60ml bottle</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info">Medicine</span></td>
                            <td>₱80.00</td>
                            <td>18</td>
                            <td><span class="badge bg-success">In Stock</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 5) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 5) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>EQP001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-thermometer text-danger me-2"></i>
                                    <div>
                                        <strong>Digital Thermometer</strong>
                                        <br><small class="text-muted">Electronic thermometer</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">Equipment</span></td>
                            <td>₱350.00</td>
                            <td>3</td>
                            <td><span class="badge bg-warning">Low Stock</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', 6) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', 6) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Products pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Stock Summary -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>Total Products</h5>
                            <h2>156</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box-seam fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>In Stock</h5>
                            <h2>142</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>Low Stock</h5>
                            <h2>12</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>Out of Stock</h5>
                            <h2>2</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
