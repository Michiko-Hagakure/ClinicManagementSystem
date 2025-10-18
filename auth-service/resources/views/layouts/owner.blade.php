<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Owner Dashboard') - Clinic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #00A689;
            --secondary-color: #00c9a0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, #00A689);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0.375rem;
            margin: 0.25rem 0.5rem;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar-menu a i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f9fa;
        }

        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .content-wrapper {
            padding: 0 2rem 2rem 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card.financial {
            border-left-color: #10b981;
        }

        .stat-card.patients {
            border-left-color: #3b82f6;
        }

        .stat-card.inventory {
            border-left-color: #f59e0b;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .date-filter-btn {
            border-radius: 20px;
            padding: 0.5rem 1.25rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .date-filter-btn.active {
            background: #00A689 !important;
            color: white !important;
            border-color: #00A689 !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('owner.dashboard') }}" class="d-flex align-items-center mb-2 text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">Owner Portal</span>
            </a>
        </div>

        <div class="text-center my-3 px-3">
            <img src="{{ auth()->user()->profile_picture_url }}" alt="Profile" 
                 class="rounded-circle mb-2" 
                 style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
            <h6 class="text-white mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-white-50">{{ auth()->user()->getRoleDisplayName() }}</small>
        </div>

        <hr style="background-color: rgba(255, 255, 255, 0.3); border: none; height: 1px; margin: 1rem 0;">
        
        <div class="sidebar-menu" style="flex: 1; overflow-y: auto;">
            <a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('owner.reports.emr') }}" class="{{ request()->routeIs('owner.reports.emr') ? 'active' : '' }}">
                <i class="bi bi-heart-pulse"></i>
                <span>EMR Reports</span>
            </a>
            <a href="{{ route('owner.reports.pos') }}" class="{{ request()->routeIs('owner.reports.pos') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                <span>POS Reports</span>
            </a>
            <a href="{{ route('owner.reports.inventory') }}" class="{{ request()->routeIs('owner.reports.inventory') ? 'active' : '' }}">
                <i class="bi bi-capsule"></i>
                <span>Inventory Reports</span>
            </a>
        </div>

        <hr style="background-color: rgba(255, 255, 255, 0.3); border: none; height: 1px; margin: 1rem 0;">

        <div class="p-3">
            <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-light w-100" onclick="return confirmLogout(event)">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                    <small class="text-muted">Consolidated clinic reports and analytics</small>
                </div>
                <div>
                    <span class="badge bg-primary">{{ auth()->user()->getRoleDisplayName() }}</span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00A689',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = event.target.closest('a').href;
                }
            });
            return false;
        }
    </script>
    @yield('scripts')
</body>
</html>

