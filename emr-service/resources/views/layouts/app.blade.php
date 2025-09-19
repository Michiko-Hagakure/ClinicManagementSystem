<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EMR System')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Fixed Sidebar -->
        <div class="sidebar sidebar-custom d-flex flex-column flex-shrink-0 p-2 position-fixed" style="width: 220px; height: 100vh; z-index: 1000;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">EMR System</span>
            </a>

            <div class="text-center my-3">
                <img src="{{ asset('images/avatar.jpg') }}" alt="User Avatar" 
                    class="rounded-circle mb-2" width="50" height="50">
                <h6 class="text-white mb-0">{{ session('user_name', 'Name') }}</h6>
                <small class="text-white-50">{{ ucfirst(str_replace('_', ' ', session('user_role', 'clinic staff'))) }}</small>
            </div>

            <hr>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1">
                    <a href="{{ route('dashboard') }}" class="nav-link py-2 {{ request()->is('/') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('patients.index') }}" class="nav-link py-2 {{ request()->is('patients*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i>Patient Records
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('consultations.index') }}" class="nav-link py-2 {{ request()->is('consultations*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-medical me-2"></i>Consultation Notes
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('lab-records.index') }}" class="nav-link py-2 {{ request()->is('lab-records*') ? 'active' : '' }}">
                        <i class="bi bi-card-image me-2"></i>Lab Records
                    </a>
                </li>
            

            </ul>
            <hr>
            
            <!-- Logout Button -->
            <a href="http://127.0.0.1:8000/logout" class="nav-link py-2 w-100 text-start text-white">
                <i class="bi bi-box-arrow-right me-2"></i>Log out
            </a>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1" style="margin-left: 220px; width: calc(100% - 220px);">
            <div class="container-fluid h-100 p-2">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>