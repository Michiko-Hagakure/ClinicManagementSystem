@extends('layouts.app')

@section('title', 'Inventory Reports - Pharmacy System')

@section('page-title', 'Inventory Reports')
@section('page-description', 'Generate and export inventory and dispensation reports')

@section('content')
<div class="container-fluid">
    <!-- Date Range Filter with Quick Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-calendar-range me-2"></i>Report Period</h6>
                <small>{{ now()->format('F d, Y - g:i A') }}</small>
            </div>
        </div>
        <div class="card-body">
            <!-- Quick Date Filters -->
            <div class="mb-3">
                <label class="form-label fw-bold"><i class="bi bi-lightning-fill me-1"></i>Quick Filters</label>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('today')">Today</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('week')">This Week</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('month')">This Month</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange('year')">This Year</button>
                </div>
            </div>

            <!-- Custom Date Range -->
            <form method="GET" action="{{ route('inventory.reports') }}" id="dateRangeForm" class="row g-3">
                <div class="col-md-5">
                    <label for="from_date" class="form-label fw-bold">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="from_date" 
                           name="from_date" 
                           value="{{ request('from_date', now()->subDays(30)->toDateString()) }}">
                </div>
                <div class="col-md-5">
                    <label for="to_date" class="form-label fw-bold">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="to_date" 
                           name="to_date" 
                           value="{{ request('to_date', now()->toDateString()) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section: Overview Statistics -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-dark mb-0"><i class="bi bi-graph-up me-2"></i>Overview Statistics</h5>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print Report
        </button>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Medicines</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['total_medicines'] }}</h3>
                        </div>
                        <i class="bi bi-capsule" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Inventory Value</h6>
                            <h3 class="mb-0 mt-2">₱{{ number_format($stats['total_inventory_value'], 2) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Low Stock Items</h6>
                            <h3 class="mb-0 mt-2">{{ $stats['low_stock_count'] }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Dispensed (Period)</h6>
                            <h3 class="mb-0 mt-2">{{ number_format($stats['total_dispensed_period']) }}</h3>
                        </div>
                        <i class="bi bi-box-arrow-right" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Divider -->
    <hr class="my-4">

    <!-- Section: Analytics & Charts -->
    <h5 class="text-dark mb-3"><i class="bi bi-bar-chart-line me-2"></i>Analytics & Charts</h5>
    <div class="row mb-4">
        <!-- Dispensation Trend Chart -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Dispensation Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="dispensationTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Stock Status Distribution Chart -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Stock Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Dispensed Medicines Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Top 10 Dispensed Medicines</h5>
                </div>
                <div class="card-body">
                    <canvas id="topMedicinesChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Divider -->
    <hr class="my-4">

    <!-- Section: Detailed Reports -->
    <h5 class="text-dark mb-3"><i class="bi bi-file-earmark-text me-2"></i>Detailed Reports</h5>
    <div class="row">
        <!-- Inventory Status Report -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Inventory Status Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View current stock levels, low stock items, and out of stock medicines.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Current stock levels</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Low stock warnings</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Total inventory value</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('medicine.index') }}" class="btn btn-primary flex-fill">
                            <i class="bi bi-eye me-1"></i>View Report
                        </a>
                        <a href="{{ route('inventory.export', 'medicines') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispensation Report -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Dispensation Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Track medicine dispensing activities and patient medication history.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Dispensation history</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Patient-wise tracking</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Date range filtering</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('pharmacy.history') }}" class="btn btn-success flex-fill">
                            <i class="bi bi-eye me-1"></i>View Report
                        </a>
                        <a href="{{ route('inventory.export', 'dispensations') }}" class="btn btn-outline-success">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Report -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alert Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitor medicines below minimum stock levels and reorder recommendations.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Critical stock levels</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Reorder suggestions</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Alert history</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('inventory.low-stock') }}" class="btn btn-warning flex-fill text-white">
                            <i class="bi bi-eye me-1"></i>View Report
                        </a>
                        <a href="{{ route('inventory.export', 'low-stock') }}" class="btn btn-outline-warning">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispensation Summary Report -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Dispensation Summary</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Detailed dispensation analysis with filters and statistical insights.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success me-2"></i>Top dispensed medicines</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Cost analysis</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Trend analysis</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex gap-2">
                        <a href="{{ route('inventory.dispensation-report') }}" class="btn btn-info flex-fill text-white">
                            <i class="bi bi-eye me-1"></i>View Report
                        </a>
                        <a href="{{ route('inventory.export', 'dispensations') }}?detailed=1" class="btn btn-outline-info">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Divider -->
    <hr class="my-4">

    <!-- Section: Top Performers & Alerts -->
    <h5 class="text-dark mb-3"><i class="bi bi-trophy me-2"></i>Top Performers & Critical Alerts</h5>

    <!-- Top Dispensed Medicines (Period) -->
    @if($topDispensed->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Dispensed Medicines ({{ request('from_date', now()->subDays(30)->toDateString()) }} to {{ request('to_date', now()->toDateString()) }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th class="text-end">Total Dispensed</th>
                                <th class="text-end">Current Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDispensed as $index => $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $item->medicine->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->medicine->dosage ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ number_format($item->total_quantity) }} units</span>
                                    </td>
                                    <td class="text-end">{{ $item->medicine->stock_quantity ?? 0 }} units</td>
                                    <td>
                                        @if($item->medicine && $item->medicine->stock_quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($item->medicine && $item->medicine->isLowStock())
                                            <span class="badge bg-warning">Low Stock</span>
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
        </div>
    @endif

    <!-- Low Stock Medicines -->
    @if($lowStockMedicines->count() > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Critical Low Stock Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockMedicines as $medicine)
                                <tr class="{{ $medicine->stock_quantity == 0 ? 'table-danger' : 'table-warning' }}">
                                    <td><strong>{{ $medicine->name }}</strong></td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $medicine->stock_quantity == 0 ? 'danger' : 'warning' }}">
                                            {{ $medicine->stock_quantity }} units
                                        </span>
                                    </td>
                                    <td class="text-end">₱{{ number_format($medicine->price, 2) }}</td>
                                    <td>
                                        <a href="{{ route('medicine.edit', $medicine) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>Restock
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Quick Date Range Filters
function setDateRange(range) {
    const today = new Date();
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    
    let fromDate, toDate;
    
    switch(range) {
        case 'today':
            fromDate = today;
            toDate = today;
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
            fromDate = weekStart;
            toDate = today;
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = today;
            break;
        case 'year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = today;
            break;
        default:
            return;
    }
    
    // Format dates as YYYY-MM-DD
    fromDateInput.value = fromDate.toISOString().split('T')[0];
    toDateInput.value = toDate.toISOString().split('T')[0];
    
    // Submit the form
    document.getElementById('dateRangeForm').submit();
}

// Print functionality
function printReport(reportType) {
    window.print();
}

// Remove auto-submit on date change (let users click Update button instead)
// This prevents accidental submissions while selecting dates

// Chart.js Global Configuration
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = true;

// 1. Dispensation Trend Line Chart
const dispensationData = @json($dailyDispensation);
const dates = dispensationData.map(item => item.dispensation_date);
const quantities = dispensationData.map(item => item.total_quantity);

const ctxLine = document.getElementById('dispensationTrendChart').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Units Dispensed',
            data: quantities,
            borderColor: 'rgb(52, 152, 219)',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(52, 152, 219)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' units';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                },
                title: {
                    display: true,
                    text: 'Quantity (units)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});

// 2. Stock Status Doughnut Chart
const stockData = @json($stockDistribution);
const ctxDoughnut = document.getElementById('stockStatusChart').getContext('2d');
new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
        labels: ['In Stock', 'Low Stock', 'Out of Stock'],
        datasets: [{
            data: [stockData.in_stock, stockData.low_stock, stockData.out_of_stock],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',    // Green for In Stock
                'rgba(255, 193, 7, 0.8)',     // Yellow for Low Stock
                'rgba(220, 53, 69, 0.8)'      // Red for Out of Stock
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// 3. Top Dispensed Medicines Horizontal Bar Chart
const topMedicines = @json($topDispensed);
const medicineNames = topMedicines.map(item => {
    const name = item.medicine ? item.medicine.name : 'N/A';
    const dosage = item.medicine ? item.medicine.dosage : '';
    return name + (dosage ? ' - ' + dosage : '');
});
const medicineQuantities = topMedicines.map(item => item.total_quantity);

const ctxBar = document.getElementById('topMedicinesChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: medicineNames,
        datasets: [{
            label: 'Total Dispensed',
            data: medicineQuantities,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(199, 199, 199, 0.7)',
                'rgba(83, 102, 255, 0.7)',
                'rgba(255, 99, 255, 0.7)',
                'rgba(99, 255, 132, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)',
                'rgba(83, 102, 255, 1)',
                'rgba(255, 99, 255, 1)',
                'rgba(99, 255, 132, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Dispensed: ' + context.parsed.x + ' units';
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                },
                title: {
                    display: true,
                    text: 'Quantity Dispensed (units)'
                }
            }
        }
    }
});
</script>
@endsection
