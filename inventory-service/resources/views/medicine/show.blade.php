@extends('layouts.app')

@section('title', 'Medicine Details - Mary Angels Diagnostic Clinic')
@section('page-title', 'Medicine Details')
@section('page-description', 'View detailed information about this medicine')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-capsule me-2"></i>{{ $medicine->name }} ({{ $medicine->dosage }})
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Medicine Name</h6>
                        <p class="h5">{{ $medicine->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Dosage</h6>
                        <p class="h5">{{ $medicine->dosage }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Category</h6>
                        <p><span class="badge bg-secondary fs-6">{{ $medicine->category }}</span></p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Price</h6>
                        <p class="h5">₱{{ number_format($medicine->price, 2) }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Stock Quantity</h6>
                        <p class="h5">{{ $medicine->stock_quantity }} units</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Status</h6>
                        <p>
                            @if($medicine->stock_quantity == 0)
                                <span class="badge bg-danger fs-6">Out of Stock</span>
                            @elseif($medicine->isLowStock())
                                <span class="badge bg-warning fs-6">Low Stock</span>
                            @else
                                <span class="badge bg-success fs-6">In Stock</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted mb-2">Total Value</h6>
                        <p class="h5">₱{{ number_format($medicine->stock_quantity * $medicine->price, 2) }}</p>
                    </div>
                </div>

                <hr>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('medicine.edit', $medicine) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Medicine
                    </a>
                    <button type="button" class="btn btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#stockModal"
                            data-id="{{ $medicine->medicine_id }}"
                            data-name="{{ $medicine->name }}">
                        <i class="bi bi-plus-circle me-1"></i>Add Stock
                    </button>
                    <a href="{{ route('medicine.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>

        @if(isset($recentDispensations) && $recentDispensations->count() > 0)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Recent Dispensation History
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDispensations as $dispensation)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dispensation->date)->format('M d, Y') }}</td>
                                    <td>{{ $dispensation->quantity }} units</td>
                                    <td>{{ $dispensation->reason ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Quick Info
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Added On</small>
                    <p class="mb-0">{{ $medicine->created_at->format('M d, Y g:i A') }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Last Updated</small>
                    <p class="mb-0">{{ $medicine->updated_at->format('M d, Y g:i A') }}</p>
                </div>
                @if($medicine->lowStockAlerts->count() > 0)
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Low Stock Alert Active</strong>
                    <p class="mb-0 small">This medicine has low stock and needs restocking.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-calculator me-2"></i>Statistics
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Total Dispensed</small>
                    <p class="h5 mb-0">{{ $medicine->dispensedMedicines->sum('quantity') ?? 0 }} units</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Dispensation Count</small>
                    <p class="h5 mb-0">{{ $medicine->dispensedMedicines->count() }} times</p>
                </div>
                <div>
                    <small class="text-muted">Stock Value</small>
                    <p class="h5 mb-0">₱{{ number_format($medicine->stock_quantity * $medicine->price, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="stockForm">
            @csrf
            <input type="hidden" name="action" value="add">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Medicine</label>
                        <input type="text" class="form-control" id="medicineName" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Quantity to add</label>
                        <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Stock</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var stockModal = document.getElementById('stockModal');
    if (!stockModal) return;
    stockModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var medicineId = button.getAttribute('data-id');
        var medicineName = button.getAttribute('data-name');
        document.getElementById('medicineName').value = medicineName;
        var form = document.getElementById('stockForm');
        form.action = '/medicine/' + medicineId + '/update-stock';
    });
});
</script>
@endsection

