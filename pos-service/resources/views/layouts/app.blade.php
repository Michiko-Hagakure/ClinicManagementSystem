<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons - Updated Version -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        .sidebar-custom {
            background: linear-gradient(to bottom, #00A689);
            color: black;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
            color: white !important;
        }
        .sidebar-custom hr {
            background-color: rgba(255, 255, 255, 0.3);
            border: none;
            height: 1px;
        }
        main {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 0.375rem;
        }
        .btn-success {
            background: #28a745;
            border: none;
        }
        .stats-card {
            background: #00A689;
            color: white;
        }
    </style>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Fixed Sidebar -->
        <div class="sidebar sidebar-custom d-flex flex-column flex-shrink-0 p-2 position-fixed" style="width: 220px; height: 100vh; z-index: 1000;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">POS System</span>
            </a>

            <div class="text-center my-3">
                <img src="{{ asset('images/avatar.jpg') }}" alt="User Avatar" 
                    class="rounded-circle mb-2" width="50" height="50">
                <h6 class="text-white mb-0">{{ session('user_name', 'Name') }}</h6>
                <small class="text-white-50">{{ ucfirst(str_replace('_', ' ', session('user_role', 'cashier'))) }}</small>
            </div>

            <hr>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1">
                    <a href="{{ route('dashboard') }}" class="nav-link py-2 {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('transactions.create') }}" class="nav-link py-2 {{ request()->is('transactions/create') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin me-2"></i>New Transaction
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('transactions.index') }}" class="nav-link py-2 {{ request()->is('transactions*') && !request()->is('transactions/create') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i>Transaction History
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('pharmacy.sales') }}" class="nav-link py-2 {{ request()->is('pharmacy*') ? 'active' : '' }}">
                        <i class="bi bi-prescription2 me-2"></i>Medicine Sales
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('patients.lookup') }}" class="nav-link py-2 {{ request()->is('patients*') ? 'active' : '' }}">
                        <i class="bi bi-search me-2"></i>Patient Lookup
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="#" class="nav-link py-2 {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up me-2"></i>Reports
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="http://127.0.0.1:8000/logout" class="nav-link py-2 w-100 text-start text-white-50">
                        <i class="bi bi-box-arrow-right me-2"></i>Sign out
                    </a>
                </li>
            </ul>
            <hr>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1" style="margin-left: 220px; width: calc(100% - 220px);">
            <div class="container-fluid h-100 p-2">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    @yield('scripts')
</body>
</html>