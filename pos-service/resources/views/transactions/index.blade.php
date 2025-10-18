@extends('layouts.app')

@section('title', 'Transaction History - POS System')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Transaction History</h1>
        <p class="text-muted mb-0">View and manage all billing transactions</p>
    </div>
    <div>
        <button class="btn btn-success me-2" onclick="location.href='{{ route('transactions.create') }}'">
            <i class="bi bi-plus-circle me-1"></i>New Transaction
        </button>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search patient name..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="payment_method">
                    <option value="">All Payments</option>
                    <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Card" {{ request('payment_method') == 'Card' ? 'selected' : '' }}>Card</option>
                    <option value="Insurance" {{ request('payment_method') == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="card shadow">
    <div class="card-header bg-light">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>Recent Transactions
                </h6>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary">{{ $transactions->total() }} transactions</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date & Time</th>
                            <th>Patient</th>
                            <th>Services</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <strong>{{ $transaction->id }}</strong>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</small>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="text-primary fw-bold">{{ \Carbon\Carbon::parse($transaction->created_at)->format('g:i A') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person text-white small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $transaction->patient_name }}</div>
                                        <small class="text-muted">ID: {{ $transaction->patient_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="services-list">
                                    @if(!empty($transaction->services))
                                        @foreach($transaction->services as $service)
                                            @if(($service['category'] ?? '') === 'Medicine')
                                                <span class="badge bg-success me-1 mb-1">
                                                    <i class="bi bi-capsule me-1"></i>{{ $service['name'] }}
                                                    @if(($service['quantity'] ?? 1) > 1)
                                                        <span class="badge bg-light text-dark ms-1">x{{ $service['quantity'] }}</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="badge bg-primary me-1 mb-1">{{ $service['name'] }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-muted">No services</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                <small class="text-muted">
                                    @php
                                        $hasServices = false;
                                        $hasMedicines = false;
                                        foreach($transaction->services ?? [] as $service) {
                                            if (($service['category'] ?? '') === 'Medicine') {
                                                $hasMedicines = true;
                                            } else {
                                                $hasServices = true;
                                            }
                                        }
                                        
                                        if ($hasServices && $hasMedicines) {
                                            echo 'Services & Medicines';
                                        } elseif ($hasMedicines) {
                                            echo 'Medicines Only';
                                        } elseif ($hasServices) {
                                            echo 'Services Only';
                                        } else {
                                            echo 'No items';
                                        }
                                    @endphp
                                </small>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    @if($transaction->payment_method == 'Cash')
                                        <span class="badge bg-success mb-1"><i class="bi bi-cash me-1"></i>{{ $transaction->payment_method }}</span>
                                    @elseif(in_array($transaction->payment_method, ['Credit_card', 'Debit_card', 'Card']))
                                        <span class="badge bg-info mb-1"><i class="bi bi-credit-card me-1"></i>Card</span>
                                    @else
                                        <span class="badge bg-secondary mb-1">{{ $transaction->payment_method }}</span>
                                    @endif
                                    <small class="text-muted">{{ $transaction->cashier }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ $transaction->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="viewTransaction('{{ $transaction->id }}')"
                                            title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="printReceipt('{{ $transaction->id }}')"
                                            title="Print Receipt">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-receipt display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No transactions found</h5>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'payment_method']))
                    <p class="text-muted">Try adjusting your filter criteria.</p>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                    </a>
                @else
                    <p class="text-muted">Start processing your first transaction.</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Create First Transaction
                    </a>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} 
                        of {{ $transactions->total() }} results
                    </small>
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Summary Stats -->
@if($transactions->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $transactions->count() }}</h4>
                <small>Total Transactions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>₱{{ number_format($transactions->sum('total_amount'), 2) }}</h4>
                <small>Total Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>₱{{ number_format($transactions->avg('total_amount'), 2) }}</h4>
                <small>Average Transaction</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $transactions->where('status', 'Paid')->count() }}</h4>
                <small>Paid Transactions</small>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
function viewTransaction(transactionId) {
    // Redirect to transaction details page
    window.location.href = `/transactions/${transactionId}`;
}

function printReceipt(transactionId) {
    // Open receipt in new window for printing
    const receiptUrl = `/transactions/${transactionId}?print=1`;
    const printWindow = window.open(receiptUrl, '_blank', 'width=800,height=600');
    
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Auto-refresh every 60 seconds to show new transactions
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 60000);

// Quick keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'n':
                e.preventDefault();
                location.href = '{{ route('transactions.create') }}';
                break;
            case 'p':
                e.preventDefault();
                window.print();
                break;
        }
    }
});
</script>
@endsection