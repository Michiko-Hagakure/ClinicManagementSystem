@extends('layouts.app')

@section('title', 'Transaction Details - POS System')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Transaction Details</h1>
        <p class="text-muted mb-0">Receipt #{{ $transaction->receipt_number }}</p>
    </div>
    <div>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i>Back to Transactions
        </a>
        <button class="btn btn-success" onclick="printReceipt()">
            <i class="bi bi-printer me-1"></i>Print Receipt
        </button>
    </div>
</div>

<!-- Transaction Details Card -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-receipt me-2"></i>Transaction Information
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Patient Information</h6>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $transaction->patient_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Patient ID:</strong></td>
                        <td>{{ $transaction->patient_id }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Transaction Information</h6>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Transaction ID:</strong></td>
                        <td>{{ $transaction->transaction_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cashier:</strong></td>
                        <td>{{ $transaction->cashier }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>{{ $transaction->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
@if(!empty($transaction->services))
<div class="card shadow mt-4">
    <div class="card-header bg-info text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-heart-pulse me-2"></i>Services
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->services as $service)
                    <tr>
                        <td>{{ $service['name'] }}</td>
                        <td class="text-end">₱{{ number_format($service['price'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th>Services Subtotal</th>
                        <th class="text-end">₱{{ number_format($transaction->service_total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Medicines Section -->
@if(!empty($transaction->medicines))
<div class="card shadow mt-4">
    <div class="card-header bg-warning text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-capsule me-2"></i>Medicines
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->medicines as $medicine)
                    <tr>
                        <td>{{ $medicine['name'] }}</td>
                        <td class="text-center">{{ $medicine['quantity'] }}</td>
                        <td class="text-end">₱{{ number_format($medicine['price'], 2) }}</td>
                        <td class="text-end">₱{{ number_format($medicine['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="3">Medicines Subtotal</th>
                        <th class="text-end">₱{{ number_format($transaction->medicine_total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Payment Summary -->
<div class="card shadow mt-4">
    <div class="card-header bg-success text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-cash-coin me-2"></i>Payment Summary
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td>{{ ucfirst($transaction->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount Paid:</strong></td>
                        <td>₱{{ number_format($transaction->amount_paid, 2) }}</td>
                    </tr>
                    @if($transaction->change_amount > 0)
                    <tr>
                        <td><strong>Change:</strong></td>
                        <td class="text-success">₱{{ number_format($transaction->change_amount, 2) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h4 class="text-success mb-0">₱{{ number_format($transaction->total_amount, 2) }}</h4>
                        <small class="text-muted">Total Amount</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function printReceipt() {
    // Open the new traditional receipt design for printing in a clean window
    const receiptUrl = '{{ route("transactions.receipt", $transaction->receipt_number ?? "receipt") }}';
    const printWindow = window.open(
        receiptUrl, 
        'receipt_print', 
        'width=400,height=700,scrollbars=yes,resizable=yes,location=no,menubar=no,toolbar=no,status=no'
    );
    
    // Focus the print window and ensure it's clean
    if (printWindow) {
        printWindow.focus();
        
        // Optional: Auto-close after printing (uncomment if desired)
        // printWindow.addEventListener('afterprint', function() {
        //     printWindow.close();
        // });
    } else {
        // Fallback if popup was blocked
        alert('Please allow popups for receipt printing');
    }
}
</script>
@endsection
