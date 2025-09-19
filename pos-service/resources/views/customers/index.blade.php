@extends('layouts.app')

@section('title', 'Customers - Mary Angels Diagnostic Clinic')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Customer Management</h1>
            <small class="text-muted">Mary Angels Diagnostic Clinic - Manage Customer Information</small>
        </div>
        <div>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Add New Customer
            </a>
        </div>
    </div>

    <!-- Customer Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">847</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
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
                                Active Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">723</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x text-gray-300"></i>
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
                                New This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">47</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fa-2x text-gray-300"></i>
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
                                VIP Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">23</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search customers...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option>All Types</option>
                        <option>Regular</option>
                        <option>VIP</option>
                        <option>New</option>
                        <option>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option>All Locations</option>
                        <option>Barangay 1</option>
                        <option>Barangay 2</option>
                        <option>Barangay 3</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="Registration Date">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="bi bi-person-lines-fill me-2"></i>Customer Directory
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Total Purchases</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>CUST-001</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2">MS</div>
                                    <div>
                                        <strong>Maria Santos</strong><br>
                                        <small class="text-muted">Age: 34</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-phone me-1"></i>0917-123-4567<br>
                                    <i class="bi bi-envelope me-1"></i>maria.santos@email.com
                                </div>
                            </td>
                            <td>
                                <div>
                                    Barangay 1, Quezon City<br>
                                    <small class="text-muted">Metro Manila</small>
                                </div>
                            </td>
                            <td><span class="badge bg-success">Regular</span></td>
                            <td>
                                <div>
                                    <strong>₱12,450.00</strong><br>
                                    <small class="text-muted">23 transactions</small>
                                </div>
                            </td>
                            <td>
                                {{ now()->subDays(2)->format('M d, Y') }}<br>
                                <small class="text-muted">2 days ago</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', 1) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>CUST-002</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-info text-white me-2">JD</div>
                                    <div>
                                        <strong>Juan Dela Cruz</strong><br>
                                        <small class="text-muted">Age: 42</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-phone me-1"></i>0918-234-5678<br>
                                    <i class="bi bi-envelope me-1"></i>juan.delacruz@email.com
                                </div>
                            </td>
                            <td>
                                <div>
                                    Barangay 2, Marikina City<br>
                                    <small class="text-muted">Metro Manila</small>
                                </div>
                            </td>
                            <td><span class="badge bg-info">New</span></td>
                            <td>
                                <div>
                                    <strong>₱1,200.00</strong><br>
                                    <small class="text-muted">1 transaction</small>
                                </div>
                            </td>
                            <td>
                                {{ now()->format('M d, Y') }}<br>
                                <small class="text-muted">Today</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', 2) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>CUST-003</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-warning text-white me-2">AR</div>
                                    <div>
                                        <strong>Ana Rodriguez</strong><br>
                                        <small class="text-muted">Age: 28</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-phone me-1"></i>0919-345-6789<br>
                                    <i class="bi bi-envelope me-1"></i>ana.rodriguez@email.com
                                </div>
                            </td>
                            <td>
                                <div>
                                    Barangay 1, Pasig City<br>
                                    <small class="text-muted">Metro Manila</small>
                                </div>
                            </td>
                            <td><span class="badge bg-warning text-dark">VIP</span></td>
                            <td>
                                <div>
                                    <strong>₱28,750.00</strong><br>
                                    <small class="text-muted">45 transactions</small>
                                </div>
                            </td>
                            <td>
                                {{ now()->subDays(1)->format('M d, Y') }}<br>
                                <small class="text-muted">1 day ago</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', 3) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>CUST-004</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2">CM</div>
                                    <div>
                                        <strong>Carlos Mendoza</strong><br>
                                        <small class="text-muted">Age: 56</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-phone me-1"></i>0920-456-7890<br>
                                    <i class="bi bi-envelope me-1"></i>carlos.mendoza@email.com
                                </div>
                            </td>
                            <td>
                                <div>
                                    Barangay 3, Makati City<br>
                                    <small class="text-muted">Metro Manila</small>
                                </div>
                            </td>
                            <td><span class="badge bg-warning text-dark">VIP</span></td>
                            <td>
                                <div>
                                    <strong>₱45,200.00</strong><br>
                                    <small class="text-muted">67 transactions</small>
                                </div>
                            </td>
                            <td>
                                {{ now()->format('M d, Y') }}<br>
                                <small class="text-muted">Today</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', 4) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>CUST-005</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white me-2">LG</div>
                                    <div>
                                        <strong>Lisa Garcia</strong><br>
                                        <small class="text-muted">Age: 31</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-phone me-1"></i>0921-567-8901<br>
                                    <i class="bi bi-envelope me-1"></i>lisa.garcia@email.com
                                </div>
                            </td>
                            <td>
                                <div>
                                    Barangay 2, Taguig City<br>
                                    <small class="text-muted">Metro Manila</small>
                                </div>
                            </td>
                            <td><span class="badge bg-success">Regular</span></td>
                            <td>
                                <div>
                                    <strong>₱8,750.00</strong><br>
                                    <small class="text-muted">16 transactions</small>
                                </div>
                            </td>
                            <td>
                                {{ now()->subDays(5)->format('M d, Y') }}<br>
                                <small class="text-muted">5 days ago</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', 5) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-receipt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Customers pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

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
</style>
@endsection
