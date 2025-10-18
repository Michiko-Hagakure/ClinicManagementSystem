@extends('layouts.owner')

@section('title', 'POS Reports')
@section('page-title', 'Point of Sale - Financial Reports')

@section('content')
<!-- Date Range Filter -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.reports.pos') }}" id="dateFilterForm">
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

<!-- Financial Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card financial">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Revenue</div>
                    <h2 class="mb-0 fw-bold text-success">₱{{ number_format($financialData['total_revenue'] ?? 0, 2) }}</h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card financial">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Transactions</div>
                    <h2 class="mb-0 fw-bold text-primary">{{ number_format($financialData['total_transactions'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card financial">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Paid Bills</div>
                    <h2 class="mb-0 fw-bold text-info">₱{{ number_format($financialData['paid_bills'] ?? 0, 2) }}</h2>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card financial">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Unpaid Bills</div>
                    <h2 class="mb-0 fw-bold text-warning">₱{{ number_format($financialData['unpaid_bills'] ?? 0, 2) }}</h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row">
    <!-- Revenue Trend Chart -->
    <div class="col-md-8 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-graph-up text-success me-2"></i>Revenue Trend
            </h5>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>

    <!-- Payment Methods Distribution -->
    <div class="col-md-4 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-pie-chart text-primary me-2"></i>Payment Methods
            </h5>
            <canvas id="paymentMethodsChart"></canvas>
        </div>
    </div>
</div>

<!-- Payment Methods Table -->
@if(!empty($financialData['payment_methods']) && count($financialData['payment_methods']) > 0)
<div class="chart-container">
    <h5 class="mb-3">
        <i class="bi bi-credit-card text-success me-2"></i>Payment Method Breakdown
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Payment Method</th>
                    <th class="text-center">Transactions</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = array_sum(array_column($financialData['payment_methods'], 'total_amount'));
                @endphp
                @foreach($financialData['payment_methods'] as $method)
                <tr>
                    <td>
                        <i class="bi bi-{{ $method['payment_method'] == 'cash' ? 'cash' : 'credit-card' }} me-2 text-success"></i>
                        <strong>{{ ucfirst($method['payment_method'] ?? 'N/A') }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $method['count'] ?? 0 }}</span>
                    </td>
                    <td class="text-end fw-semibold text-success">
                        ₱{{ number_format($method['total_amount'] ?? 0, 2) }}
                    </td>
                    <td class="text-end">
                        <span class="badge bg-success">
                            {{ $totalAmount > 0 ? number_format((($method['total_amount'] ?? 0) / $totalAmount) * 100, 1) : 0 }}%
                        </span>
                    </td>
                </tr>
                @endforeach
                <tr class="table-light fw-bold">
                    <td colspan="2">TOTAL</td>
                    <td class="text-end text-success">₱{{ number_format($totalAmount, 2) }}</td>
                    <td class="text-end">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No payment data available for the selected period.
</div>
@endif

<!-- Services Rendered Table -->
@if(!empty($financialData['services_rendered']) && count($financialData['services_rendered']) > 0)
<div class="chart-container mt-4">
    <h5 class="mb-3">
        <i class="bi bi-list-check text-success me-2"></i>Top Services by Revenue
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Service Name</th>
                    <th class="text-center">Quantity Sold</th>
                    <th class="text-end">Total Revenue</th>
                    <th class="text-end">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalServiceRevenue = array_sum(array_column($financialData['services_rendered'], 'revenue'));
                @endphp
                @foreach($financialData['services_rendered'] as $service)
                <tr>
                    <td>
                        <i class="bi bi-bag-check me-2 text-success"></i>
                        <strong>{{ $service['service_name'] ?? 'N/A' }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary fs-6">{{ $service['count'] ?? 0 }}</span>
                    </td>
                    <td class="text-end fw-semibold text-success">
                        ₱{{ number_format($service['revenue'] ?? 0, 2) }}
                    </td>
                    <td class="text-end">
                        <span class="badge bg-success">
                            {{ $totalServiceRevenue > 0 ? number_format((($service['revenue'] ?? 0) / $totalServiceRevenue) * 100, 1) : 0 }}%
                        </span>
                    </td>
                </tr>
                @endforeach
                <tr class="table-light fw-bold">
                    <td colspan="2" class="text-end">TOTAL</td>
                    <td class="text-end text-success">₱{{ number_format($totalServiceRevenue, 2) }}</td>
                    <td class="text-end">100%</td>
                </tr>
            </tbody>
        </table>
    </div>
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

    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueTrends = {!! json_encode($financialData['revenue_trends'] ?? []) !!};
        const labels = Object.keys(revenueTrends);
        const data = Object.values(revenueTrends);
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Pie Chart
    const paymentCtx = document.getElementById('paymentMethodsChart');
    if (paymentCtx) {
        const paymentData = {!! json_encode($financialData['payment_methods'] ?? []) !!};
        const labels = paymentData.map(p => (p.payment_method || 'N/A').toUpperCase());
        const amounts = paymentData.map(p => p.total_amount || 0);
        
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [{
                    data: amounts.length > 0 ? amounts : [1],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₱' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection

