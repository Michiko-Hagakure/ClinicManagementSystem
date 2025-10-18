@extends('layouts.owner')

@section('title', 'EMR Reports')
@section('page-title', 'Electronic Medical Records - Patient Statistics')

@section('content')
<!-- Date Range Filter -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('owner.reports.emr') }}" id="dateFilterForm">
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

<!-- Patient Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card patients">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Patients</div>
                    <h2 class="mb-0 fw-bold text-primary">{{ number_format($patientData['total_patients'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card patients">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">New Patients</div>
                    <h2 class="mb-0 fw-bold text-success">{{ number_format($patientData['new_patients'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card patients">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Total Consultations</div>
                    <h2 class="mb-0 fw-bold text-info">{{ number_format($patientData['total_consultations'] ?? 0) }}</h2>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="stat-card patients">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted mb-1">Avg. Daily Visits</div>
                    <h2 class="mb-0 fw-bold text-warning">
                        {{ count($patientData['daily_visits'] ?? []) > 0 ? number_format(array_sum(array_column($patientData['daily_visits'], 'count')) / count($patientData['daily_visits']), 1) : 0 }}
                    </h2>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row">
    <!-- Daily Patient Visits -->
    <div class="col-md-8 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-bar-chart text-primary me-2"></i>Daily Patient Visits
            </h5>
            <canvas id="patientVisitsChart" height="80"></canvas>
        </div>
    </div>

    <!-- Chief Complaints Distribution -->
    <div class="col-md-4 mb-4">
        <div class="chart-container">
            <h5 class="mb-3">
                <i class="bi bi-pie-chart text-info me-2"></i>Chief Complaints Distribution
            </h5>
            <canvas id="consultationChart"></canvas>
        </div>
    </div>
</div>

<!-- Chief Complaints / Consultation Reasons Table -->
@if(!empty($patientData['services_rendered']) && count($patientData['services_rendered']) > 0)
<div class="chart-container">
    <h5 class="mb-3">
        <i class="bi bi-clipboard2-pulse text-info me-2"></i>Top Chief Complaints
        <small class="text-muted">(Consultation Reasons)</small>
    </h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Chief Complaint</th>
                    <th class="text-center">Occurrences</th>
                    <th class="text-end">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patientData['services_rendered'] as $service)
                <tr>
                    <td>
                        <i class="bi bi-heart-pulse me-2 text-info"></i>
                        <strong>{{ $service['service_name'] ?? 'N/A' }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info fs-6">{{ $service['count'] ?? 0 }}</span>
                    </td>
                    <td class="text-end">
                        <span class="badge bg-primary">
                            {{ number_format((($service['count'] ?? 0) / ($patientData['total_consultations'] ?: 1)) * 100, 1) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle me-2"></i>
        <small><strong>Note:</strong> This shows the most common reasons patients visited the clinic. For billing/service revenue, check the POS Reports.</small>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>No consultation data available for the selected period.
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

    // Patient Visits Chart
    const visitsCtx = document.getElementById('patientVisitsChart');
    if (visitsCtx) {
        const visitsData = {!! json_encode($patientData['daily_visits'] ?? []) !!};
        const labels = visitsData.map(v => v.date || 'N/A');
        const data = visitsData.map(v => v.count || 0);
        
        new Chart(visitsCtx, {
            type: 'bar',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [{
                    label: 'Patient Visits',
                    data: data.length > 0 ? data : [0],
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Visits: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Chief Complaints Distribution Chart
    const consultationCtx = document.getElementById('consultationChart');
    if (consultationCtx) {
        const servicesRendered = {!! json_encode($patientData['services_rendered'] ?? []) !!};
        const labels = servicesRendered.map(s => s.service_name || 'N/A');
        const data = servicesRendered.map(s => s.count || 0);
        
        new Chart(consultationCtx, {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [{
                    data: data.length > 0 ? data : [1],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                        '#06b6d4',
                        '#f97316'
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
                            padding: 8,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection

