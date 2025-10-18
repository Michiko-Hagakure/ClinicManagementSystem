@extends('layouts.app')

@section('title', 'Dispensation History - Pharmacy System')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="bi bi-clock-history me-2"></i>Dispensation History
            </h1>
            <p class="text-muted mb-0">Track all medicine dispensing activities</p>
        </div>
        <a href="{{ route('pharmacy.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Dispensations</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pharmacy.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="patient_id" class="form-label">Patient ID</label>
                    <input type="number" 
                           class="form-control" 
                           id="patient_id" 
                           name="patient_id" 
                           placeholder="Enter patient ID" 
                           value="{{ request('patient_id') }}">
                </div>
                <div class="col-md-3">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="from_date" 
                           name="from_date" 
                           value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="to_date" 
                           name="to_date" 
                           value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['patient_id', 'from_date', 'to_date']))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <div class="flex-grow-1">
                <strong>Active Filters:</strong>
                @if(request('patient_id'))
                    <span class="badge bg-primary ms-2">Patient ID: {{ request('patient_id') }}</span>
                @endif
                @if(request('from_date'))
                    <span class="badge bg-primary ms-2">From: {{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }}</span>
                @endif
                @if(request('to_date'))
                    <span class="badge bg-primary ms-2">To: {{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}</span>
                @endif
            </div>
            <a href="{{ route('pharmacy.history') }}" class="btn btn-sm btn-outline-secondary ms-2">Clear Filters</a>
        </div>
    @endif

    <!-- Dispensation Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>Dispensation Records
                </h6>
                <span class="badge bg-light text-dark">Total: {{ $dispensations->total() }} records</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($dispensations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Date & Time</th>
                                <th>Patient ID</th>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dispensations as $dispensation)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $dispensation->date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $dispensation->date->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">P{{ str_pad($dispensation->patient_id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $dispensation->medicine->name ?? 'Unknown' }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $dispensation->medicine->dosage ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $dispensation->quantity }} units</span>
                                    </td>
                                    <td class="text-end">
                                        ₱{{ number_format($dispensation->medicine->price ?? 0, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">₱{{ number_format($dispensation->total_cost ?? 0, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>Summary:</strong></td>
                                <td class="text-end">
                                    <strong>{{ $dispensations->sum('quantity') }} units</strong>
                                </td>
                                <td colspan="1"></td>
                                <td class="text-end">
                                    <strong class="text-success">
                                        ₱{{ number_format($dispensations->sum('total_cost'), 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($dispensations->hasPages())
                    <div class="d-flex justify-content-end align-items-center p-3 border-top bg-light">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- Previous Button --}}
                                @if ($dispensations->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" style="padding: 0.25rem 0.5rem;">‹ Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $dispensations->appends(request()->except('page'))->previousPageUrl() }}" style="padding: 0.25rem 0.5rem;">‹ Previous</a>
                                    </li>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach ($dispensations->getUrlRange(1, $dispensations->lastPage()) as $page => $url)
                                    @if ($page == $dispensations->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link" style="padding: 0.25rem 0.5rem;">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $dispensations->appends(request()->except('page'))->url($page) }}" style="padding: 0.25rem 0.5rem;">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Button --}}
                                @if ($dispensations->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $dispensations->appends(request()->except('page'))->nextPageUrl() }}" style="padding: 0.25rem 0.5rem;">Next ›</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" style="padding: 0.25rem 0.5rem;">Next ›</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Dispensation Records Found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['patient_id', 'from_date', 'to_date']))
                            Try adjusting your filters to see more results.
                        @else
                            No medicines have been dispensed yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($dispensations->count() > 0)
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-calendar-event me-2"></i>Period
                        </h6>
                        <p class="card-text fs-5 mb-0">
                            {{ $dispensations->first()->date->format('M d') }} - {{ $dispensations->last()->date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-capsule me-2"></i>Total Items Dispensed
                        </h6>
                        <p class="card-text fs-5 mb-0">
                            {{ number_format($dispensations->sum('quantity')) }} units
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-cash-stack me-2"></i>Total Value
                        </h6>
                        <p class="card-text fs-5 mb-0">
                            ₱{{ number_format($dispensations->sum('total_cost'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when date changes (optional UX enhancement)
    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');
    
    if (fromDate && toDate) {
        fromDate.addEventListener('change', function() {
            if (toDate.value && this.value) {
                this.form.submit();
            }
        });
        
        toDate.addEventListener('change', function() {
            if (fromDate.value && this.value) {
                this.form.submit();
            }
        });
    }
});
</script>
@endsection
