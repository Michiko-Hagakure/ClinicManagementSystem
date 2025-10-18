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
                        <h4 class="text-primary" id="currentTime">{{ now()->format('g:i A') }}</h4>
                        <small class="text-muted">Current Time</small>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <strong>Clinic Hours:</strong><br>
                        <small class="text-muted">7:00 AM - 5:00 PM</small>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong><br>
                        <span id="clinicStatus" class="badge">
                        @if(now()->hour >= 7 && now()->hour < 17)
                            <span class="badge bg-success">Open</span>
                        @else
                            <span class="badge bg-danger">Closed</span>
                        @endif
                        </span>
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
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 12%;">Time</th>
                                    <th style="width: 25%;">Patient</th>
                                    <th style="width: 30%;">Services</th>
                                    <th style="width: 15%;">Amount</th>
                                    <th style="width: 18%;">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr class="align-middle">
                                    <td>
                                        <div class="text-primary fw-bold">{{ $transaction->created_at->format('g:i A') }}</div>
                                        <small class="text-muted">{{ $transaction->created_at->format('M d') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-white small"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->patient_name }}</div>
                                                <small class="text-muted">{{ $transaction->patient_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" title="{{ $transaction->services }}">
                                            {{ $transaction->services }}
                                        </div>
                                        @if($transaction->service_count > 1)
                                            <small class="text-muted">{{ $transaction->service_count }} services</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                        @if($transaction->change_amount > 0)
                                            <small class="text-muted">Change: ₱{{ number_format($transaction->change_amount, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-start">
                                            @if($transaction->payment_method == 'cash')
                                                <span class="badge bg-success mb-1"><i class="bi bi-cash me-1"></i>Cash</span>
                                            @elseif($transaction->payment_method == 'gcash')
                                                <span class="badge bg-primary mb-1"><i class="bi bi-phone me-1"></i>GCash</span>
                                            @elseif($transaction->payment_method == 'credit_card')
                                                <span class="badge bg-info mb-1"><i class="bi bi-credit-card me-1"></i>Card</span>
                                            @else
                                                <span class="badge bg-secondary mb-1">{{ ucfirst($transaction->payment_method) }}</span>
                                            @endif
                                            <small class="text-muted">{{ $transaction->cashier_name }}</small>
                                        </div>
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
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if(isset($pendingActions) && $pendingActions->count() > 0)
                    @foreach($pendingActions as $action)
                    <div class="d-flex align-items-start mb-3 p-2 rounded {{ $action->urgency == 'high' ? 'bg-danger-subtle border border-danger' : ($action->urgency == 'medium' ? 'bg-warning-subtle border border-warning' : 'bg-light border') }}">
                        <div class="flex-shrink-0 me-3">
                            @if($action->urgency == 'high')
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                            @elseif($action->urgency == 'medium')
                                <i class="bi bi-clock-fill text-warning fs-5"></i>
                            @else
                                <i class="bi bi-clock text-muted fs-5"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 fw-bold">{{ $action->patient_name }}</h6>
                                <span class="badge bg-primary">{{ $action->patient_code }}</span>
                            </div>
                            <div class="text-muted small mb-1">
                                <strong>Bill #{{ $action->bill_number }}</strong> • {{ $action->first_service }}
                                @if($action->service_count > 1)
                                    <span class="text-primary">+{{ $action->service_count - 1 }} more</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-success">₱{{ number_format($action->total_amount, 2) }}</span>
                                    <small class="text-muted ms-2">
                                        @if($action->days_old == 0)
                                            Today
                                        @elseif($action->days_old == 1)
                                            1 day ago
                                        @else
                                            {{ $action->days_old }} days ago
                                        @endif
                                    </small>
                                </div>
                                <button class="btn btn-sm {{ $action->urgency == 'high' ? 'btn-danger' : 'btn-outline-primary' }}" 
                                        onclick="processBill({{ $action->id }}, '{{ $action->bill_number }}', {{ $action->total_amount }})">
                                    @if($action->urgency == 'high')
                                        <i class="bi bi-lightning-fill me-1"></i>Urgent
                                    @else
                                        <i class="bi bi-credit-card me-1"></i>Process
                                    @endif
                                </button>
                        </div>
                            @if($action->due_date)
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Due: {{ $action->due_date->format('M d, Y') }}
                                    </small>
                        </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        <h6 class="mt-2 mb-1">All caught up!</h6>
                        <p class="text-muted mb-0">No pending payments at the moment.</p>
                        <small class="text-muted">Great work keeping everything up to date!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Real-time clock and status updates
    function updateTimeAndStatus() {
        const now = new Date();
        
        // Update current time
        const timeString = now.toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('currentTime').textContent = timeString;
        
        // Update clinic status based on current hour
        const currentHour = now.getHours();
        const statusElement = document.getElementById('clinicStatus');
        
        if (currentHour >= 7 && currentHour < 17) {
            statusElement.innerHTML = '<span class="badge bg-success">Open</span>';
        } else {
            statusElement.innerHTML = '<span class="badge bg-danger">Closed</span>';
        }
    }
    
    // Update time immediately when page loads
    updateTimeAndStatus();
    
    // Update time every second
    setInterval(updateTimeAndStatus, 1000);
    
    // Auto-refresh dashboard data every 5 minutes (but not the whole page to preserve real-time clock)
    setInterval(function() {
        // Only refresh if user is active (to avoid unnecessary requests)
        if (document.hasFocus()) {
        location.reload();
        }
    }, 300000); // 5 minutes
    
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
            }
        }
    });
    
    // Show notification when clinic status changes
    let lastStatus = null;
    function checkStatusChange() {
        const now = new Date();
        const currentHour = now.getHours();
        const currentStatus = (currentHour >= 7 && currentHour < 17) ? 'open' : 'closed';
        
        if (lastStatus !== null && lastStatus !== currentStatus) {
            // Status changed, show notification
            const message = currentStatus === 'open' ? 
                'Clinic is now OPEN for business!' : 
                'Clinic is now CLOSED. Have a great day!';
                
            // You can add a toast notification here if needed
            console.log(message);
        }
        
        lastStatus = currentStatus;
    }
    
    // Check for status changes every minute
    setInterval(checkStatusChange, 60000);
    checkStatusChange(); // Initial check
    
    // Function to handle processing pending bills
    function processBill(billId, billNumber, amount) {
        // Show confirmation dialog
        if (confirm(`Process payment for Bill #${billNumber}?\nAmount: ₱${amount.toLocaleString('en-US', {minimumFractionDigits: 2})}`)) {
            // In a real implementation, you would redirect to the payment processing page
            // For now, we'll just show an alert and potentially redirect
            alert(`Redirecting to process Bill #${billNumber}...`);
            
            // Redirect to transaction creation with pre-filled data
            // You can modify this URL based on your actual transaction processing route
            window.location.href = `/transactions/create?bill_id=${billId}`;
        }
    }
    
    // Auto-refresh recent transactions every 30 seconds (only the data, not the whole page)
    function refreshTransactionData() {
        // This could be implemented with AJAX to update just the transaction table
        // For now, we'll do a full page refresh every 5 minutes as implemented above
        console.log('Transaction data refresh - implement AJAX here if needed');
    }
    
    // Optional: Add hover effects for transaction rows
    document.addEventListener('DOMContentLoaded', function() {
        const transactionRows = document.querySelectorAll('table tbody tr');
        transactionRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
</script>
@endsection