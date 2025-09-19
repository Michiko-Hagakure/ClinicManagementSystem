@extends('layouts.app')

@section('title', 'Cashier Dashboard - Mary Angels Clinic')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Cashier Dashboard</h1>
        <p class="text-muted mb-0">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <div>
        <button class="btn btn-success me-2" onclick="location.href='{{ route('transactions.create') }}'">
            <i class="bi bi-plus-circle me-1"></i>New Transaction
        </button>
        <button class="btn btn-primary" onclick="location.href='{{ route('pharmacy.sales') }}'">
            <i class="bi bi-capsule me-1"></i>Medicine Sale
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Today's Revenue
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">₱{{ number_format($todayRevenue ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-peso text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Transactions Today
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">{{ $todayTransactions ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Pending Bills
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">{{ $pendingBills ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock-history text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Medicine Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-white">₱{{ number_format($medicineRevenue ?? 0, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-capsule text-white-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-primary btn-lg" onclick="location.href='{{ route('transactions.create') }}'">
                                <i class="bi bi-cash-coin d-block mb-2" style="font-size: 2rem;"></i>
                                New Bill
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-success btn-lg" onclick="location.href='{{ route('pharmacy.sales') }}'">
                                <i class="bi bi-capsule d-block mb-2" style="font-size: 2rem;"></i>
                                Sell Medicine
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-info btn-lg" onclick="location.href='{{ route('patients.lookup') }}'">
                                <i class="bi bi-person-search d-block mb-2" style="font-size: 2rem;"></i>
                                Find Patient
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-clock me-2"></i>Quick Stats
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <h4 class="text-primary">{{ now()->format('g:i A') }}</h4>
                        <small class="text-muted">Current Time</small>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <strong>Clinic Hours:</strong><br>
                        <small class="text-muted">7:00 AM - 5:00 PM</small>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong><br>
                        @if(now()->hour >= 7 && now()->hour < 17)
                            <span class="badge bg-success">Open</span>
                        @else
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history me-2"></i>Recent Transactions
                </h6>
            </div>
            <div class="card-body">
                @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Services</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('g:i A') }}</td>
                                    <td>{{ $transaction->patient_name }}</td>
                                    <td>{{ $transaction->services }}</td>
                                    <td>₱{{ number_format($transaction->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success">Paid</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No transactions yet today</p>
                        <button class="btn btn-primary" onclick="location.href='{{ route('transactions.create') }}'">
                            <i class="bi bi-plus-circle me-1"></i>Create First Transaction
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-exclamation-triangle me-2"></i>Pending Actions
                </h6>
            </div>
            <div class="card-body">
                @if(isset($pendingActions) && $pendingActions->count() > 0)
                    @foreach($pendingActions as $action)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ $action->patient_name }}</h6>
                            <small class="text-muted">{{ $action->service_type }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <button class="btn btn-sm btn-outline-primary">Process</button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">All caught up!</p>
                        <small class="text-muted">No pending actions</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh dashboard every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
    
    // Quick keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey) {
            switch(e.key) {
                case 'n':
                    e.preventDefault();
                    location.href = '{{ route('transactions.create') }}';
                    break;
                case 'm':
                    e.preventDefault();
                    location.href = '{{ route('pharmacy.sales') }}';
                    break;
                case 'f':
                    e.preventDefault();
                    location.href = '{{ route('patients.lookup') }}';
                    break;
            }
        }
    });
</script>
@endsection