@extends('layouts.owner')

@section('title', 'Inventory Reports')
@section('page-title', 'Inventory Management - Stock Reports')

@section('content')
<!-- Date Range Filter -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.reports.inventory') }}" id="dateFilterForm">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-semibold mb-3">
                        <i class="bi bi-calendar-range me-2"></i>Select Time Period:
                    </label>
                    <div class="btn-group flex-wrap" role="group">
                        <button type="button" class="btn btn-outline-primary date-filter-btn {{ $preset == 'today' ? 'active' : '' }}" 
                                onclick="setPreset('today')">
                            <i class="bi bi-calendar-day me-1"></i>Today
                        </button>
                        <button type="button" class="btn btn-outline-primary date-filter-btn {{ $preset == 'week' ? 'active' : '' }}" 
                                onclick="setPreset('week')">
                            <i class="bi bi-calendar-week me-1"></i>This Week
                        </button>
                        <button type="button" class="btn btn-outline-primary date-filter-btn {{ $preset == 'month' ? 'active' : '' }}" 
                                onclick="setPreset('month')">
                            <i class="bi bi-calendar-month me-1"></i>This Month
                        </button>
                        <button type="button" class="btn btn-outline-primary date-filter-btn {{ $preset == 'year' ? 'active' : '' }}" 
                                onclick="setPreset('year')">
                            <i class="bi bi-calendar-check me-1"></i>This Year
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Custom Date Range:</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}" required>
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Apply
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="preset" id="preset" value="{{ $preset }}">
        </form>
    </div>
</div>

<!-- Inventory Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card inventory">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Medicines</div>
                    <h2 class="mb-0 fw-bold text-primary">{{ number_format($inventoryData['total_medicines'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-capsule"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card inventory">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Stock Value</div>
                    <h2 class="mb-0 fw-bold text-success">₱{{ number_format($inventoryData['total_stock_value'] ?? 0, 2) }}</h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card inventory">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Low Stock Items</div>
                    <h2 class="mb-0 fw-bold text-danger">{{ number_format($inventoryData['low_stock_items'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card inventory">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Expiring Soon</div>
                    <h2 class="mb-0 fw-bold text-warning">{{ number_format($inventoryData['expiring_items'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-calendar-x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alerts Section -->
<div class="row mb-4">
    @if(($inventoryData['low_stock_items'] ?? 0) > 0)
    <div class="col-md-6 mb-3">
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <strong>Low Stock Alert!</strong><br>
                {{ $inventoryData['low_stock_items'] }} item(s) need restocking
            </div>
        </div>
    </div>
    @endif

    @if(($inventoryData['expiring_items'] ?? 0) > 0)
    <div class="col-md-6 mb-3">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-calendar-x-fill me-3 fs-4"></i>
            <div>
                <strong>Expiry Warning!</strong><br>
                {{ $inventoryData['expiring_items'] }} item(s) expiring within 3 months
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Charts Section -->
<div class="row">
    <!-- Top Selling Medicines -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-graph-up text-primary me-2"></i>High-Volume Medicine Sales
            </h5>
            <canvas id="topMedicinesChart" height="100"></canvas>
        </div>
    </div>

    <!-- Stock Status -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-pie-chart text-info me-2"></i>Stock Status Overview
            </h5>
            <canvas id="stockStatusChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Selling Medicines Table -->
@if(!empty($inventoryData['top_selling_medicines']) && count($inventoryData['top_selling_medicines']) > 0)
<div class="chart-container">
    <h5 class="mb-3">
        <i class="bi bi-graph-up-arrow text-primary me-2"></i>High-Volume Medicine Sales Report
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Demand Level</th>
                    <th>Medicine Name</th>
                    <th class="text-center">Quantity Sold</th>
                    <th class="text-end">Stock Level</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryData['top_selling_medicines'] as $index => $medicine)
                <tr>
                    <td>
                        @if($index === 0)
                            <span class="badge bg-danger">Highest Volume</span>
                        @elseif($index === 1)
                            <span class="badge bg-warning text-dark">High Demand</span>
                        @elseif($index === 2)
                            <span class="badge bg-info">Popular</span>
                        @else
                            <span class="text-muted fw-semibold">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $medicine['medicine']['name'] ?? 'N/A' }}</strong><br>
                        <small class="text-muted">{{ $medicine['medicine']['description'] ?? '' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary fs-6">{{ number_format($medicine['total_quantity_sold'] ?? 0) }}</span>
                    </td>
                    <td class="text-end">
                        {{ number_format($medicine['medicine']['stock_quantity'] ?? 0) }} units
                    </td>
                    <td class="text-center">
                        @if(($medicine['medicine']['stock_quantity'] ?? 0) <= ($medicine['medicine']['reorder_level'] ?? 0))
                            <span class="badge bg-danger">Low Stock</span>
                        @else
                            <span class="badge bg-success">In Stock</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No sales data available for the selected period.
</div>
@endif

@endsection

@section('scripts')
<script>
    // Date preset functions
    function setPreset(preset) {
        const today = new Date();
        let startDate, endDate = new Date();
        
        switch(preset) {
            case 'today':
                startDate = new Date();
                break;
            case 'week':
                startDate = new Date();
                startDate.setDate(today.getDate() - today.getDay());
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1);
                break;
        }
        
        document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
        document.getElementById('preset').value = preset;
        document.getElementById('dateFilterForm').submit();
    }

    // Top Medicines Chart
    const medicinesCtx = document.getElementById('topMedicinesChart');
    if (medicinesCtx) {
        const topMedicines = {!! json_encode($inventoryData['top_selling_medicines'] ?? []) !!};
        const labels = topMedicines.map(m => (m.medicine && m.medicine.name) || 'N/A');
        const data = topMedicines.map(m => m.total_quantity_sold || 0);
        
        new Chart(medicinesCtx, {
            type: 'bar',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [{
                    label: 'Quantity Sold',
                    data: data.length > 0 ? data : [0],
                    backgroundColor: '#f59e0b',
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Stock Status Chart
    const stockCtx = document.getElementById('stockStatusChart');
    if (stockCtx) {
        const totalMedicines = {{ $inventoryData['total_medicines'] ?? 0 }};
        const lowStock = {{ $inventoryData['low_stock_items'] ?? 0 }};
        const expiring = {{ $inventoryData['expiring_items'] ?? 0 }};
        const healthy = Math.max(0, totalMedicines - lowStock - expiring);
        
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Healthy Stock', 'Low Stock', 'Expiring Soon'],
                datasets: [{
                    data: [healthy, lowStock, expiring],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444',
                        '#f59e0b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10
                        }
                    }
                }
            }
        });
    }
</script>
@endsection

