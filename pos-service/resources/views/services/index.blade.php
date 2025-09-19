@extends('layouts.app')

@section('title', 'Medical Services - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Medical Services</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Service Catalog & Pricing</small>
        </div>
        <div>
            <a href="{{ route('services.consultations') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-stethoscope me-2"></i>Consultations
            </a>
            <a href="{{ route('services.diagnostics') }}" class="btn btn-outline-info me-2">
                <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostics
            </a>
            <a href="{{ route('services.medications') }}" class="btn btn-outline-success">
                <i class="bi bi-capsule-pill me-2"></i>Medications
            </a>
        </div>
    </div>

    <!-- Service Categories Overview -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Consultation Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">3 Services</div>
                            <div class="text-xs text-muted">₱300 - ₱800 price range</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-stethoscope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Diagnostic Services</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">6 Services</div>
                            <div class="text-xs text-muted">₱350 - ₱8,000 price range</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clipboard2-pulse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Medications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">150+ Items</div>
                            <div class="text-xs text-muted">Prescription & OTC medicines</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-capsule-pill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation Services -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="bi bi-stethoscope me-2"></i>Consultation Services
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-check text-primary fa-3x mb-3"></i>
                            <h5>General Consultation</h5>
                            <p class="text-muted">General medical consultation with our experienced doctors. Includes basic health assessment and medical advice.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">Standard</span>
                                <h4 class="text-success mb-0">₱500.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-hospital text-info fa-3x mb-3"></i>
                            <h5>Specialist Consultation</h5>
                            <p class="text-muted">Specialized medical consultation for specific health conditions requiring expert medical attention.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-info">Specialist</span>
                                <h4 class="text-success mb-0">₱800.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-arrow-repeat text-warning fa-3x mb-3"></i>
                            <h5>Follow-up Visit</h5>
                            <p class="text-muted">Follow-up consultation for ongoing treatment monitoring and medical care continuity.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning text-dark">Follow-up</span>
                                <h4 class="text-success mb-0">₱300.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnostic Services -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic Services
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-droplet text-danger me-2"></i>
                                    <strong>Laboratory Tests</strong>
                                </div>
                            </td>
                            <td>Blood work, urine tests, and other laboratory analyses</td>
                            <td><span class="badge bg-danger">Laboratory</span></td>
                            <td>30-60 mins</td>
                            <td><strong class="text-success">₱450.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-x-ray text-secondary me-2"></i>
                                    <strong>X-ray Imaging</strong>
                                </div>
                            </td>
                            <td>Digital radiological imaging for bone and organ assessment</td>
                            <td><span class="badge bg-secondary">Radiology</span></td>
                            <td>15-30 mins</td>
                            <td><strong class="text-success">₱650.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-soundwave text-info me-2"></i>
                                    <strong>Ultrasound</strong>
                                </div>
                            </td>
                            <td>Non-invasive imaging using sound waves</td>
                            <td><span class="badge bg-info">Imaging</span></td>
                            <td>20-45 mins</td>
                            <td><strong class="text-success">₱1,200.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-heart-pulse text-danger me-2"></i>
                                    <strong>ECG/EKG</strong>
                                </div>
                            </td>
                            <td>Electrocardiogram for heart function assessment</td>
                            <td><span class="badge bg-warning text-dark">Cardiology</span></td>
                            <td>10-15 mins</td>
                            <td><strong class="text-success">₱350.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cpu text-primary me-2"></i>
                                    <strong>CT Scan</strong>
                                </div>
                            </td>
                            <td>Computed Tomography for detailed cross-sectional imaging</td>
                            <td><span class="badge bg-primary">Advanced Imaging</span></td>
                            <td>30-60 mins</td>
                            <td><strong class="text-success">₱3,500.00</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-magnet text-success me-2"></i>
                                    <strong>MRI</strong>
                                </div>
                            </td>
                            <td>Magnetic Resonance Imaging for detailed soft tissue imaging</td>
                            <td><span class="badge bg-success">Advanced Imaging</span></td>
                            <td>45-90 mins</td>
                            <td><strong class="text-success">₱8,000.00</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('billing.create') }}" class="btn btn-primary w-100">
                                <i class="bi bi-receipt-cutoff me-2"></i>
                                Create New Bill
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('patients.index') }}" class="btn btn-info w-100">
                                <i class="bi bi-person-heart me-2"></i>
                                Find Patient
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('invoices.index') }}" class="btn btn-success w-100">
                                <i class="bi bi-file-earmark-medical me-2"></i>
                                View Invoices
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.index') }}" class="btn btn-warning w-100">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                Generate Reports
                            </a>
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

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
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

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection
