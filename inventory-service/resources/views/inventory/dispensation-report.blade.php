@extends('layouts.app')

@section('title', 'Dispensation Report')

@section('page-title', 'Dispensation Summary Report')
@section('page-description', 'Detailed dispensation analysis with filters and statistical insights')

@section('content')
<div class="container-fluid">
    <!-- Back Button & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('inventory.reports') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
        <div class="btn-group">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <a href="{{ route('inventory.export', 'dispensations') }}?{{ http_build_query(request()->all()) }}" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.dispensation-report') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="from_date" class="form-label fw-bold">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="from_date" 
                           name="from_date" 
                           value="{{ request('from_date', now()->subDays(30)->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label fw-bold">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="to_date" 
                           name="to_date" 
                           value="{{ request('to_date', now()->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <label for="patient_id" class="form-label fw-bold">Patient ID (Optional)</label>
                    <input type="text" 
                           class="form-control" 
                           id="patient_id" 
                           name="patient_id" 
                           value="{{ request('patient_id') }}"
                           placeholder="Filter by patient ID">
                </div>
                <div class="col-md-3">
                    <label for="medicine_id" class="form-label fw-bold">Medicine (Optional)</label>
                    <select class="form-select" id="medicine_id" name="medicine_id">
                        <option value="">All Medicines</option>
                        @foreach(\App\Models\Medicine::orderBy('name')->get() as $medicine)
                            <option value="{{ $medicine->id }}" {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{ $medicine->name }} - {{ $medicine->dosage }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('inventory.dispensation-report') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Dispensations</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($summary['total_dispensations']) }}</h2>
                            <small>Number of transactions</small>
                        </div>
                        <i class="bi bi-receipt" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Quantity</h6>
                            <h2 class="mb-0 mt-2">{{ number_format($summary['total_quantity']) }}</h2>
                            <small>Units dispensed</small>
                        </div>
                        <i class="bi bi-box-arrow-right" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Estimated Revenue</h6>
                            <h2 class="mb-0 mt-2">₱{{ number_format($summary['estimated_revenue'], 2) }}</h2>
                            <small>Based on unit prices</small>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispensation Records Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Dispensation Records</h5>
                <span class="badge bg-white text-info">{{ $dispensations->total() }} records</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($dispensations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th class="text-center">Quantity</th>
                                <th>Patient ID</th>
                                <th>Dispensed By</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Value</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dispensations as $dispense)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($dispense->date)->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($dispense->date)->format('g:i A') }}</small>
                                    </td>
                                    <td><strong>{{ $dispense->medicine->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $dispense->medicine->dosage ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $dispense->quantity }} units</span>
                                    </td>
                                    <td>
                                        @if($dispense->patient_id)
                                            <span class="badge bg-secondary">P{{ str_pad($dispense->patient_id, 4, '0', STR_PAD_LEFT) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $dispense->dispensed_by ?? 'Staff' }}</small>
                                    </td>
                                    <td class="text-end">₱{{ number_format($dispense->medicine->price ?? 0, 2) }}</td>
                                    <td class="text-end">
                                        <strong>₱{{ number_format(($dispense->medicine->price ?? 0) * $dispense->quantity, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($dispense->notes)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($dispense->notes, 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $dispensations->firstItem() ?? 0 }} to {{ $dispensations->lastItem() ?? 0 }} 
                            of {{ $dispensations->total() }} dispensations
                        </div>
                        {{ $dispensations->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No Dispensation Records Found</h5>
                    <p class="text-muted">Try adjusting your filters or select a different date range.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow-sm mt-4 bg-light">
        <div class="card-body">
            <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Report Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Includes all medicine dispensations within the selected period
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Revenue estimates based on current medicine prices
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Filter by patient ID to track individual medication history
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Export to Excel for further analysis
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    @media print {
        .btn, .pagination, .card-footer, .card.shadow-sm:first-child {
            display: none !important;
        }
    }
</style>
@endsection

