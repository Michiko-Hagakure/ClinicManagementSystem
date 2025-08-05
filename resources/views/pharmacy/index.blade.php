@extends('layouts.app')

@section('title', 'Pharmacy Inventory')

@section('content')
    {{-- Page Header --}}
    <div class="header p-3 mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Pharmacy Inventory Management</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
            + Add New Medicine
        </button>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Medicine Types</h5>
                    <p class="card-text fs-4 fw-bold">152</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Items Low on Stock</h5>
                    <p class="card-text fs-4 fw-bold">8</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card with Inventory List --}}
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Search by medicine name...">
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Medicine Name</th>
                            <th>Dosage</th>
                            <th>Price</th>
                            <th>Quantity in Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Placeholder Data --}}
                        <tr>
                            <td>Paracetamol</td>
                            <td>500mg</td>
                            <td>₱4.50</td>
                            <td>250</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                                <a href="#" class="btn btn-secondary btn-sm">Adjust Stock</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Amoxicillin</td>
                            <td>250mg</td>
                            <td>₱12.00</td>
                            <td class="text-danger fw-bold">15</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                                <a href="#" class="btn btn-secondary btn-sm">Adjust Stock</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Loratadine</td>
                            <td>10mg</td>
                            <td>₱25.00</td>
                            <td>88</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                                <a href="#" class="btn btn-secondary btn-sm">Adjust Stock</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicineModalLabel">Add New Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Medicine Name</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage (e.g., 500mg)</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Stock Quantity</label>
                        <input type="number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Low-Stock Alert Threshold</label>
                        <input type="number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Medicine</button>
                </div>
            </div>
        </div>
    </div>
@endsection