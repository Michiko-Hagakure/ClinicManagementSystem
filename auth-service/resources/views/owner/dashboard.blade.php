@extends('layouts.owner')

@section('title', 'Owner Dashboard')
@section('page-title', 'Clinic Overview & Reports')

@section('content')
<!-- Date Range Filter -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.dashboard') }}" id="dateFilterForm">
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

<!-- Summary Statistics -->
<div class="row mb-4">
    <!-- Financial Summary -->
    <div class="col-md-4 mb-3">
        <div class="stat-card financial">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Revenue</div>
                    <h2 class="mb-0 fw-bold text-success">₱{{ number_format($financialData['total_revenue'] ?? 0, 2) }}</h2>
                    <small class="text-muted">
                        <i class="bi bi-receipt me-1"></i>
                        {{ $financialData['total_bills'] ?? 0 }} transactions
                    </small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-success">
                        <i class="bi bi-check-circle me-1"></i>Paid: {{ $financialData['paid_bills'] ?? 0 }}
                    </span>
                    <span class="text-warning">
                        <i class="bi bi-clock-history me-1"></i>Unpaid: {{ $financialData['unpaid_bills'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Statistics -->
    <div class="col-md-4 mb-3">
        <div class="stat-card patients">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Patients</div>
                    <h2 class="mb-0 fw-bold text-primary">{{ number_format($patientData['total_patients'] ?? 0) }}</h2>
                    <small class="text-muted">
                        <i class="bi bi-person-plus me-1"></i>
                        {{ $patientData['new_patients'] ?? 0 }} new patients
                    </small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-primary">
                        <i class="bi bi-clipboard2-pulse me-1"></i>Consultations: {{ $patientData['total_consultations'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Summary -->
    <div class="col-md-4 mb-3">
        <div class="stat-card inventory">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Inventory Value</div>
                    <h2 class="mb-0 fw-bold text-warning">₱{{ number_format($inventoryData['stock_value'] ?? 0, 2) }}</h2>
                    <small class="text-muted">
                        <i class="bi bi-box me-1"></i>
                        {{ $inventoryData['total_items'] ?? 0 }} items in stock
                    </small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>Low Stock: {{ $inventoryData['low_stock_items'] ?? 0 }}
                    </span>
                    <span class="text-warning">
                        <i class="bi bi-calendar-x me-1"></i>Expiring: {{ $inventoryData['expiring_soon'] ?? 0 }}
                    </span>
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

<div class="row">
    <!-- Patient Visits Trend -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i>Patient Visits
            </h5>
            <canvas id="patientVisitsChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Selling Medicines -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-capsule text-warning me-2"></i>Top Selling Medicines
            </h5>
            <canvas id="topMedicinesChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Services Rendered Table -->
@if(!empty($financialData['services_rendered']) && count($financialData['services_rendered']) > 0)
<div class="chart-container">
    <h5 class="mb-3">
        <i class="bi bi-list-check text-success me-2"></i>Services Rendered (Billed Services)
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Service Name</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalRevenue = array_sum(array_column($financialData['services_rendered'], 'revenue'));
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
                    <td class="text-end">
                        <span class="fw-semibold text-success">₱{{ number_format($service['revenue'] ?? 0, 2) }}</span>
                        <small class="text-muted d-block">
                            {{ $totalRevenue > 0 ? number_format((($service['revenue'] ?? 0) / $totalRevenue) * 100, 1) : 0 }}% of total
                        </small>
                    </td>
                </tr>
                @endforeach
                <tr class="table-light fw-bold">
                    <td colspan="2" class="text-end">TOTAL</td>
                    <td class="text-end text-success">₱{{ number_format($totalRevenue, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No service billing data available for the selected period.
</div>
@endif

<!-- Export Button -->
<div class="text-end mt-4">
    <button onclick="openPrintReport()" class="btn btn-lg btn-primary">
        <i class="bi bi-file-earmark-pdf me-2"></i>Export Report
    </button>
</div>

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
                startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
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

    // Open print report in popup window
    function openPrintReport() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const url = '{{ route("owner.reports.export") }}?start_date=' + startDate + '&end_date=' + endDate;
        
        // Open in a centered popup window
        const width = 1200;
        const height = 800;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;
        
        window.open(url, 'PrintReport', 
            'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + 
            ',toolbar=no,menubar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    }

    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($financialData['daily_revenue'] ?? [], 'date')) !!},
                datasets: [{
                    label: 'Daily Revenue',
                    data: {!! json_encode(array_column($financialData['daily_revenue'] ?? [], 'amount')) !!},
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
                        position: 'bottom'
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

    // Patient Visits Chart
    const visitsCtx = document.getElementById('patientVisitsChart');
    if (visitsCtx) {
        new Chart(visitsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($patientData['daily_visits'] ?? [], 'date')) !!},
                datasets: [{
                    label: 'Patient Visits',
                    data: {!! json_encode(array_column($patientData['daily_visits'] ?? [], 'count')) !!},
                    backgroundColor: '#3b82f6',
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
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Top Medicines Chart
    const medicinesCtx = document.getElementById('topMedicinesChart');
    if (medicinesCtx) {
        const topMedicines = {!! json_encode($inventoryData['top_selling'] ?? []) !!};
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
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
</script>
@endsection

