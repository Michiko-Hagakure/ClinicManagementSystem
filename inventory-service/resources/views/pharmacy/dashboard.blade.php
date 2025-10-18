@extends('layouts.app')

@section('title', 'Pharmacy Dashboard - Mary Angels Diagnostic Clinic')
@section('page-title', 'Pharmacy Dashboard')
@section('page-description', 'Overview of medicine inventory and pharmacy operations')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stat-value">{{ $stats['total_medicines'] }}</div>
            <div class="stat-label">
                <i class="bi bi-capsule me-1"></i>Total Medicines
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="stat-value">{{ $stats['low_stock_count'] }}</div>
            <div class="stat-label">
                <i class="bi bi-exclamation-triangle me-1"></i>Low Stock Alerts
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="stat-value">{{ $stats['out_of_stock_count'] }}</div>
            <div class="stat-label">
                <i class="bi bi-x-circle me-1"></i>Out of Stock
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="stat-value">{{ $stats['total_dispensed_today'] }}</div>
            <div class="stat-label">
                <i class="bi bi-prescription2 me-1"></i>Dispensed Today
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-charge me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('medicine.create') }}" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Add New Medicine
                        </a>
                    </div>
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('inventory.reports') }}" class="btn btn-warning w-100">
                            <i class="bi bi-graph-up me-2"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Medicine Inventory -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Medicine Inventory</span>
                <a href="{{ route('medicine.index') }}" class="btn btn-sm btn-outline-light">View All</a>
            </div>
            <div class="card-body p-0">
                @if($medicines->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicines->take(10) as $medicine)
                                    <tr class="@if($medicine->stock_quantity == 0) out-of-stock @elseif($medicine->isLowStock()) low-stock @else in-stock @endif">
                                        <td>
                                            <strong>{{ $medicine->name }}</strong>
                                        </td>
                                        <td>{{ $medicine->dosage }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $medicine->stock_quantity }}</span> units
                                        </td>
                                        <td>{{ $medicine->formatted_price }}</td>
                                        <td>
                                            @if($medicine->stock_quantity == 0)
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @elseif($medicine->isLowStock())
                                                <span class="badge bg-warning">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">In Stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('medicine.show', $medicine) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('medicine.edit', $medicine) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="text-muted mt-2">No medicines in inventory</h5>
                        <a href="{{ route('medicine.index') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-capsule me-2"></i>Go to Manage Inventory
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Alerts & Recent Activity -->
    <div class="col-lg-4 mb-4">
        <!-- Low Stock Alerts -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</span>
                <a href="{{ route('pharmacy.alerts') }}" class="btn btn-sm btn-outline-light">View All</a>
            </div>
            <div class="card-body p-0">
                @if($lowStockAlerts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockAlerts->take(5) as $alert)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $alert->medicine->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $alert->medicine->dosage ?? 'N/A' }} - Stock: {{ $alert->medicine->stock_quantity ?? 0 }}</small>
                                </div>
                                <span class="badge bg-warning rounded-pill">{{ $alert->alert_date->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle-fill text-success display-6"></i>
                        <p class="text-muted mb-0 mt-2">No low stock alerts</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Dispensations -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Dispensations</span>
                <a href="{{ route('pharmacy.history') }}" class="btn btn-sm btn-outline-light">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentDispensations->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentDispensations->take(5) as $dispensation)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $dispensation->medicine->name ?? 'Unknown Medicine' }}</h6>
                                    <small>{{ $dispensation->date->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">Patient #{{ $dispensation->patient_id }} - Qty: {{ $dispensation->quantity }}</p>
                                <small class="text-muted">{{ $dispensation->medicine->dosage ?? 'N/A' }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-prescription2 text-muted display-6"></i>
                        <p class="text-muted mb-0 mt-2">No recent dispensations</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Quick search functionality removed - not part of pharmacy staff use case
</script>
@endsection
