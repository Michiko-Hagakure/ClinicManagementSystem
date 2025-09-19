@extends('layouts.app')

@section('title', 'Reports - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Sales Reports & Analytics</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Business Intelligence Dashboard</small>
        </div>
        <div>
            <button class="btn btn-success me-2">
                <i class="bi bi-download me-2"></i>Export Report
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Report Period Selector -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Report Period:</label>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option>Today</option>
                        <option>Yesterday</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Generate
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱342,150.00</div>
                            <div class="text-xs text-success">
                                <i class="bi bi-arrow-up me-1"></i>12.5% vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">1,247</div>
                            <div class="text-xs text-success">
                                <i class="bi bi-arrow-up me-1"></i>8.2% vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Sale Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱274.50</div>
                            <div class="text-xs text-success">
                                <i class="bi bi-arrow-up me-1"></i>4.1% vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Items Sold</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3,891</div>
                            <div class="text-xs text-success">
                                <i class="bi bi-arrow-up me-1"></i>15.3% vs last month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-graph-up me-2"></i>Sales Trend - This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f8f9fa 75%), linear-gradient(-45deg, transparent 75%, #f8f9fa 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;">
                        <div class="text-center text-muted">
                            <i class="bi bi-bar-chart fa-3x mb-3"></i>
                            <h5>Sales Chart</h5>
                            <p>Interactive sales trend visualization would appear here</p>
                            <small>Integration with Chart.js or similar library needed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-trophy me-2"></i>Top Selling Products
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Paracetamol 500mg</span>
                            <span class="small text-success">₱4,650</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Vitamin C 1000mg</span>
                            <span class="small text-success">₱3,750</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 77%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Face Mask (Box)</span>
                            <span class="small text-success">₱2,400</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 49%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Digital Thermometer</span>
                            <span class="small text-success">₱2,100</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 43%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small">Alcohol 70%</span>
                            <span class="small text-success">₱1,600</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: 33%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="row">
        <!-- Sales by Category -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-pie-chart me-2"></i>Sales by Category
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Items Sold</th>
                                    <th>Revenue</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">Medicines</span>
                                    </td>
                                    <td>1,234</td>
                                    <td>₱156,750.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-info" style="width: 45.8%">45.8%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-success">Vitamins</span>
                                    </td>
                                    <td>987</td>
                                    <td>₱98,200.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-success" style="width: 28.7%">28.7%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">Supplies</span>
                                    </td>
                                    <td>654</td>
                                    <td>₱65,400.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-secondary" style="width: 19.1%">19.1%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">Equipment</span>
                                    </td>
                                    <td>321</td>
                                    <td>₱21,800.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-primary" style="width: 6.4%">6.4%</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-credit-card me-2"></i>Payment Methods Usage
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Transactions</th>
                                    <th>Amount</th>
                                    <th>% Usage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="bi bi-cash text-success me-2"></i>Cash
                                    </td>
                                    <td>687</td>
                                    <td>₱189,450.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-success" style="width: 55.1%">55.1%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-credit-card text-info me-2"></i>Credit Card
                                    </td>
                                    <td>312</td>
                                    <td>₱89,200.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-info" style="width: 25.0%">25.0%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-phone text-warning me-2"></i>GCash
                                    </td>
                                    <td>186</td>
                                    <td>₱45,300.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-warning" style="width: 14.9%">14.9%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-phone text-primary me-2"></i>PayMaya
                                    </td>
                                    <td>62</td>
                                    <td>₱18,200.00</td>
                                    <td>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-primary" style="width: 5.0%">5.0%</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Report Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Quick Report Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-bar-chart me-2"></i>
                                Detailed Sales Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-box-seam me-2"></i>
                                Inventory Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-success w-100">
                                <i class="bi bi-people me-2"></i>
                                Customer Analysis
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-warning w-100">
                                <i class="bi bi-calendar-week me-2"></i>
                                Weekly Summary
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.fa-2x {
    font-size: 2em;
}

.fa-3x {
    font-size: 3em;
}

.progress {
    background-color: #e9ecef;
}

.chart-container {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
}
</style>
@endsection
