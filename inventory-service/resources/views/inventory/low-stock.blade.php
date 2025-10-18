@extends('layouts.app')

@section('title', 'Low Stock Alert Report')

@section('page-title', 'Low Stock Alert Report')
@section('page-description', 'Monitor medicines below minimum stock levels')

@section('content')
<div class="container-fluid">
    <!-- Back Button & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('inventory.reports') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <a href="{{ route('inventory.export', 'low-stock') }}" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Low Stock Items</h6>
                            <h2 class="mb-0 mt-2">{{ $lowStockMedicines->total() }}</h2>
                            <small>Medicines requiring attention</small>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Active Alerts</h6>
                            <h2 class="mb-0 mt-2">{{ $activeAlerts->total() }}</h2>
                            <small>Critical alerts needing action</small>
                        </div>
                        <i class="bi bi-bell-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Medicines Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Medicines</h5>
                <span class="badge bg-white text-warning">{{ $lowStockMedicines->total() }} items</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($lowStockMedicines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine Name</th>
                                <th>Dosage</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Minimum Stock</th>
                                <th class="text-end">Reorder Needed</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Restock Cost</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockMedicines as $medicine)
                                @php
                                    $reorderQty = max(50 - $medicine->stock_quantity, 0);
                                    $restockCost = $reorderQty * $medicine->price;
                                @endphp
                                <tr class="{{ $medicine->stock_quantity == 0 ? 'table-danger' : 'table-warning' }}">
                                    <td><strong>{{ $medicine->name }}</strong></td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $medicine->stock_quantity == 0 ? 'danger' : 'warning' }} text-white">
                                            {{ $medicine->stock_quantity }} units
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-muted">10 units</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ $reorderQty }} units</strong>
                                    </td>
                                    <td class="text-end">₱{{ number_format($medicine->price, 2) }}</td>
                                    <td class="text-end">
                                        <strong class="text-danger">₱{{ number_format($restockCost, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($medicine->stock_quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('medicine.edit', $medicine) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Restock Medicine">
                                            <i class="bi bi-plus-circle me-1"></i>Restock
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">Total Estimated Restock Cost:</th>
                                <th class="text-end">
                                    <span class="text-danger fs-5">
                                        ₱{{ number_format($lowStockMedicines->sum(function($m) {
                                            return max(50 - $m->stock_quantity, 0) * $m->price;
                                        }), 2) }}
                                    </span>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $lowStockMedicines->firstItem() ?? 0 }} to {{ $lowStockMedicines->lastItem() ?? 0 }} 
                            of {{ $lowStockMedicines->total() }} medicines
                        </div>
                        {{ $lowStockMedicines->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">All Medicines Are Well Stocked</h5>
                    <p class="text-muted">No medicines are below the minimum stock level.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Alerts Table -->
    @if($activeAlerts->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bell-fill me-2"></i>Active Low Stock Alerts</h5>
                    <span class="badge bg-white text-danger">{{ $activeAlerts->total() }} alerts</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Alert Date</th>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th class="text-end">Stock Level</th>
                                <th class="text-end">Threshold</th>
                                <th>Severity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeAlerts as $alert)
                                <tr>
                                    <td>
                                        <small>{{ $alert->alert_date->format('M d, Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $alert->alert_date->diffForHumans() }}</small>
                                    </td>
                                    <td><strong>{{ $alert->medicine->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $alert->medicine->dosage ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">
                                            {{ $alert->medicine->stock_quantity ?? 0 }} units
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $alert->threshold }} units</td>
                                    <td>
                                        @if($alert->medicine && $alert->medicine->stock_quantity == 0)
                                            <span class="badge bg-danger">Critical</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Warning</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alert->medicine)
                                            <a href="{{ route('medicine.edit', $alert->medicine) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-plus-circle me-1"></i>Restock
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $activeAlerts->firstItem() ?? 0 }} to {{ $activeAlerts->lastItem() ?? 0 }} 
                            of {{ $activeAlerts->total() }} alerts
                        </div>
                        {{ $activeAlerts->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Help Section -->
    <div class="card shadow-sm mt-4 bg-light">
        <div class="card-body">
            <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Reorder Recommendations</h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <strong>Minimum Stock:</strong> 10 units
                            <br><small class="text-muted">Alert triggers at this level</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <strong>Recommended Reorder:</strong> 50 units
                            <br><small class="text-muted">Standard restock quantity</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                        <div>
                            <strong>Priority:</strong> Out of Stock First
                            <br><small class="text-muted">Then low stock items</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media print {
        .btn, .pagination, .card-footer {
            display: none !important;
        }
    }
</style>
@endsection

