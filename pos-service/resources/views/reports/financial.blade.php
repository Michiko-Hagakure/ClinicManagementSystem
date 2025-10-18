@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-graph-up me-2"></i>Financial Summary Report
            </h1>
            <p class="text-muted mb-0">{{ $dateRange }}</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print Report
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                <i class="bi bi-download me-1"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('reports.financial') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $startDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $endDate }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Generate Report
                    </button>
                    <a href="{{ route('reports.financial') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>Today
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Financial Metrics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($totalRevenue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-peso text-primary" style="font-size: 2rem;"></i>
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
                                Completed Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTransactions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
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
                                Pending Bills
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTransactions }}</div>
                            <div class="text-xs text-muted">₱{{ number_format($pendingRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
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
                                Average Transaction
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($averageTransaction, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Payment Methods Breakdown -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods Breakdown</h6>
                </div>
                <div class="card-body">
                    @if($paymentMethods->count() > 0)
                        @foreach($paymentMethods as $method => $data)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    @if($method == 'Cash')
                                        <span class="badge bg-success me-2"><i class="bi bi-cash"></i></span>
                                    @elseif($method == 'Gcash')
                                        <span class="badge bg-primary me-2"><i class="bi bi-phone"></i></span>
                                    @elseif($method == 'Credit_card')
                                        <span class="badge bg-info me-2"><i class="bi bi-credit-card"></i></span>
                                    @else
                                        <span class="badge bg-secondary me-2"><i class="bi bi-wallet2"></i></span>
                                    @endif
                                    <strong>{{ $method }}</strong>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">₱{{ number_format($data['total'], 2) }}</div>
                                    <small class="text-muted">{{ $data['count'] }} transactions</small>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $totalRevenue > 0 ? ($data['total'] / $totalRevenue) * 100 : 0 }}%"
                                     aria-valuenow="{{ $data['total'] }}" aria-valuemin="0" aria-valuemax="{{ $totalRevenue }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No payment data for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Services -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Services/Items</h6>
                </div>
                <div class="card-body">
                    @if($topServices->count() > 0)
                        @foreach($topServices as $index => $service)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    <div>
                                        <div class="fw-bold">{{ $service->service_name }}</div>
                                        <small class="text-muted">{{ $service->quantity }} times</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">₱{{ number_format($service->revenue, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-list-ul text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No service data for this period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Breakdown (Today Only) -->
    @if(!empty($hourlyData) && count($hourlyData) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Hourly Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @for($hour = 7; $hour <= 17; $hour++)
                            @php
                                $data = $hourlyData->get($hour);
                                $transactions = $data ? $data->transactions : 0;
                                $revenue = $data ? $data->revenue : 0;
                                $timeLabel = date('g A', mktime($hour, 0, 0));
                            @endphp
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card border-left-{{ $transactions > 0 ? 'primary' : 'light' }} h-100">
                                    <div class="card-body py-2 px-3">
                                        <div class="text-xs font-weight-bold text-{{ $transactions > 0 ? 'primary' : 'muted' }} text-uppercase mb-1">
                                            {{ $timeLabel }}
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            ₱{{ number_format($revenue, 0) }}
                                        </div>
                                        <div class="text-xs text-muted">{{ $transactions }} transactions</div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Footer -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border-right">
                        <h5 class="text-primary mb-0">₱{{ number_format($totalRevenue, 2) }}</h5>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-right">
                        <h5 class="text-success mb-0">{{ $totalTransactions }}</h5>
                        <small class="text-muted">Transactions</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-right">
                        <h5 class="text-warning mb-0">{{ $pendingTransactions }}</h5>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5 class="text-info mb-0">₱{{ number_format($averageTransaction, 2) }}</h5>
                    <small class="text-muted">Average</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Print styles */
@media print {
    .btn, .card-header button {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
}

/* Border styles for cards */
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-light {
    border-left: 4px solid #e3e6f0 !important;
}

/* Text styling */
.text-xs {
    font-size: 0.7rem;
}
.border-right {
    border-right: 1px solid #e3e6f0;
}
</style>

<script>
function exportReport() {
    alert('PDF export functionality will be implemented.');
    // This would integrate with a PDF generation library like DomPDF or TCPDF
}

// Auto-update end date when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate && (!endDateInput.value || endDateInput.value < startDate)) {
        endDateInput.value = startDate;
    }
});

// Validate date range
document.querySelector('form').addEventListener('submit', function(e) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate && startDate > endDate) {
        e.preventDefault();
        alert('Start date cannot be later than end date.');
        return false;
    }
});
</script>
@endsection
