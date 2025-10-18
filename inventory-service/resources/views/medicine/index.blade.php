@extends('layouts.app')

@section('title', 'Manage Inventory - Mary Angels Diagnostic Clinic')
@section('page-title', 'Manage Inventory')
@section('page-description', 'Search, filter, and manage medicines in stock')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <strong><i class="bi bi-capsule me-2"></i>Medicine Inventory</strong>
            </div>
            <div class="col-md-8 text-md-end">
                <form method="GET" action="{{ route('medicine.index') }}" class="d-inline-flex align-items-center" style="gap: 8px; max-width: 100%;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name or dosage..." style="min-width: 150px;">
                    <select name="category" class="form-select form-select-sm" style="min-width: 180px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="stock_status" class="form-select form-select-sm" style="min-width: 120px;">
                        <option value="">All Stock</option>
                        <option value="available" {{ request('stock_status')==='available' ? 'selected' : '' }}>Available</option>
                        <option value="low" {{ request('stock_status')==='low' ? 'selected' : '' }}>Low</option>
                        <option value="out" {{ request('stock_status')==='out' ? 'selected' : '' }}>Out</option>
                    </select>
                    <button type="submit" class="btn btn-sm text-white p-1" style="background: none; border: none;" title="Search">
                        <i class="bi bi-search" style="font-size: 1.2rem;"></i>
                    </button>
                    <a href="{{ route('medicine.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Medicine</a>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($medicines->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Category</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Price</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 240px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                            <tr class="@if($medicine->stock_quantity == 0) out-of-stock @elseif(method_exists($medicine,'isLowStock') && $medicine->isLowStock()) low-stock @else in-stock @endif">
                                <td><strong>{{ $medicine->name }}</strong></td>
                                <td>{{ $medicine->dosage }}</td>
                                <td><span class="badge bg-secondary">{{ $medicine->category }}</span></td>
                                <td class="text-end">{{ $medicine->stock_quantity }}</td>
                                <td class="text-end">{{ $medicine->formatted_price ?? number_format($medicine->price, 2) }}</td>
                                <td>
                                    @if($medicine->stock_quantity == 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif(method_exists($medicine,'isLowStock') && $medicine->isLowStock())
                                        <span class="badge bg-warning">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">In Stock</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('medicine.edit', $medicine) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('medicine.show', $medicine) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-success" 
                                                data-bs-toggle="modal" data-bs-target="#stockModal"
                                                data-id="{{ $medicine->medicine_id }}"
                                                data-name="{{ $medicine->name }}">
                                            <i class="bi bi-plus-circle"></i> Stock
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($medicines->hasPages())
                <div class="d-flex justify-content-end align-items-center p-3 border-top bg-light">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Button --}}
                            @if ($medicines->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" style="padding: 0.25rem 0.5rem;">‹ Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $medicines->appends(request()->query())->previousPageUrl() }}" style="padding: 0.25rem 0.5rem;">‹ Previous</a>
                                </li>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($medicines->appends(request()->query())->getUrlRange(1, $medicines->lastPage()) as $page => $url)
                                @if ($page == $medicines->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link" style="padding: 0.25rem 0.5rem;">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}" style="padding: 0.25rem 0.5rem;">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Button --}}
                            @if ($medicines->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $medicines->appends(request()->query())->nextPageUrl() }}" style="padding: 0.25rem 0.5rem;">Next ›</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" style="padding: 0.25rem 0.5rem;">Next ›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="text-muted mt-2">No medicines found</h5>
            </div>
        @endif
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

