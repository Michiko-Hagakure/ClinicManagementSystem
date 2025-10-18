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
        html, body { overflow-x: hidden; }
        .sidebar-custom { background: linear-gradient(to bottom, #00A689); color: black; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar { width: 260px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.92) !important; padding: 10px 14px; border-radius: 10px; margin: 6px 12px; transition: all 0.2s ease; display: flex; align-items: center; white-space: nowrap; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(255, 255, 255, 0.2); transform: translateX(4px); color: #fff !important; }
        .sidebar-custom hr { background-color: rgba(255, 255, 255, 0.3); border: none; height: 1px; }
        .sidebar-menu { flex: 1; overflow-y: auto; overflow-x: hidden; }
        .sidebar-menu::-webkit-scrollbar { width: 6px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.35); border-radius: 4px; }
        main { background-color: #f8f9fa; min-height: 100vh; margin-left: 260px; overflow-x: hidden; box-sizing: border-box; }
        .card { border: none; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-primary { background: #007bff; border: none; border-radius: 0.375rem; }
        .btn-success { background: #28a745; border: none; }
        .stats-card { background: #00A689; color: white; }
    </style>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Fixed Sidebar -->
        <div class="sidebar sidebar-custom d-flex flex-column flex-shrink-0 p-2 position-fixed" style="height: 100vh; z-index: 1000;">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-2 text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">POS System</span>
            </a>

            <div class="text-center my-3">
                @php
                    // Get user data from session (check both formats)
                    $userName = session('user_name') ?? session('name', 'Name');
                    $userRole = session('user_role') ?? session('role', 'cashier');
                    $userId = session('user_id') ?? session('id');
                    
                    // Try to get profile picture
                    $profilePicture = session('user_profile_picture');
                    
                    // If not in session, fetch from Auth API
                    if (!$profilePicture && $userId) {
                        try {
                            $response = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:8000/api/users/' . $userId);
                            if ($response->successful()) {
                                $user = $response->json();
                                $profilePicture = $user['profile_picture_url'] ?? null;
                                session(['user_profile_picture' => $profilePicture]);
                            }
                        } catch (\Exception $e) {
                            // Silent fail
                        }
                    }
                    
                    // Set default if still null
                    if (!$profilePicture) {
                        $profilePicture = asset('images/avatar.jpg');
                    }
                    
                    // Convert localhost URLs to the correct auth service port
                    if (str_contains($profilePicture, 'localhost')) {
                        $profilePicture = str_replace('http://localhost', 'http://127.0.0.1:8000', $profilePicture);
                    }
                    // If the profile picture is a relative path from auth service
                    elseif (str_starts_with($profilePicture, '/uploads/')) {
                        $profilePicture = 'http://127.0.0.1:8000' . $profilePicture;
                    }
                @endphp
                <img src="{{ $profilePicture }}" alt="User Avatar" 
                    class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                <h6 class="text-white mb-0">{{ $userName }}</h6>
                <small class="text-white-50">{{ ucfirst(str_replace('_', ' ', $userRole)) }}</small>
            </div>

            <hr>
            
            <div class="sidebar-menu">
            <ul class="nav nav-pills flex-column mb-0">
                <li class="nav-item mb-1">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('transactions.create') }}" class="nav-link {{ request()->is('transactions/create') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin me-2"></i>New Transaction
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->is('transactions*') && !request()->is('transactions/create') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-2"></i>Transaction History
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('pharmacy.sales') }}" class="nav-link {{ request()->is('pharmacy*') ? 'active' : '' }}">
                        <i class="bi bi-prescription2 me-2"></i>Medicine Sales
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up me-2"></i>Financial Reports
                    </a>
                </li>
            </ul>
            </div>
            
            <!-- Service Status Indicator -->
            <div class="p-2 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
                @php
                    $servicesHealth = \App\Helpers\ServiceHealthHelper::getAllServicesHealth();
                @endphp
                <div class="small text-white-50 mb-2">
                    <i class="bi bi-hdd-network me-1"></i>Services Status
                </div>
                @foreach($servicesHealth as $service)
                    <div class="d-flex align-items-center justify-content-between px-2 py-1">
                        <span class="small text-white" title="{{ $service['description'] }}">
                            {{ $service['name'] }}
                        </span>
                        @if($service['status'])
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i></span>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="p-2">
                <a href="http://127.0.0.1:8000/logout" class="nav-link text-white" onclick="return confirmLogout(event)">
                    <i class="bi bi-box-arrow-right me-2"></i>Sign out
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1">
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
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        {{ session('warning') }}
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