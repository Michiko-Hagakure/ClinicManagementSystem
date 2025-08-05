@extends('layouts.app')

@section('title', 'Billing & Invoices')

@section('content')
    {{-- Page Header --}}
    <div class="header p-3 mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Billing & Invoices</h3>
        {{-- This button will trigger the modal --}}
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
            + Create New Invoice
        </button>
    </div>

    {{-- Main Content Card with Transaction List --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Placeholder Data --}}
                        <tr>
                            <td>INV-2025-001</td>
                            <td>Juan Dela Cruz</td>
                            <td>2025-08-05</td>
                            <td>₱1,550.00</td>
                            <td><span class="badge bg-success">Paid</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-secondary btn-sm">Print</a>
                            </td>
                        </tr>
                        <tr>
                            <td>INV-2025-002</td>
                            <td>Maria Clara</td>
                            <td>2025-08-04</td>
                            <td>₱800.00</td>
                            <td><span class="badge bg-warning text-dark">Unpaid</span></td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-secondary btn-sm">Print</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createInvoiceModalLabel">Create New Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Patient Selection --}}
                    <div class="mb-3">
                        <label for="patientSearch" class="form-label">Patient</label>
                        <input type="text" class="form-control" id="patientSearch" placeholder="Search for patient by name...">
                    </div>
                    <hr>
                    {{-- Line Items --}}
                    <h6>Billable Items</h6>
                    <div id="line-items-container">
                        {{-- Line items will be added here dynamically --}}
                        <div class="row g-3 align-items-center mb-2">
                            <div class="col-sm-5">
                                <input type="text" class="form-control" placeholder="Service or Medicine">
                            </div>
                            <div class="col-sm-2">
                                <input type="number" class="form-control" placeholder="Qty" value="1">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" placeholder="Price">
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-danger btn-sm">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm mt-2">+ Add Item</button>

                    {{-- Totals --}}
                    <hr>
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <strong>Subtotal:</strong> <span class="float-end">₱0.00</span><br>
                            <strong>Total Amount:</strong> <strong class="float-end">₱0.00</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Invoice</button>
                </div>
            </div>
        </div>
    </div>
@endsection