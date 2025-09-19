<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Doctor Portal - EMR System')</title>
    
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Fixed Sidebar -->
        <div class="sidebar sidebar-custom d-flex flex-column flex-shrink-0 p-2 position-fixed" style="width: 220px; height: 100vh; z-index: 1000;">
            <a href="{{ route('doctor.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">Doctor Portal</span>
            </a>

            <div class="text-center my-3">
                <img src="{{ asset('images/avatar.jpg') }}" alt="Doctor Avatar" 
                    class="rounded-circle mb-2" width="50" height="50">
                <h6 class="text-white mb-0">{{ session('user_name', 'Dr. Name') }}</h6>
                <small class="text-white-50">{{ session('user_role') === 'doctor' ? 'Physician' : ucfirst(str_replace('_', ' ', session('user_role', 'physician'))) }}</small>
            </div>

            <hr>
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.dashboard') }}" class="nav-link py-2 {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.patient-queue') }}" class="nav-link py-2 {{ request()->routeIs('doctor.patient-queue') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i>Patient Queue
                        @if(isset($pendingConsultations) && $pendingConsultations > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pendingConsultations }}</span>
                        @endif
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('patients.index') }}" class="nav-link py-2 {{ request()->is('patients*') && !request()->routeIs('doctor.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i>Patient Records
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('consultations.index') }}" class="nav-link py-2 {{ request()->is('consultations*') && !request()->routeIs('doctor.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-medical me-2"></i>My Consultations
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.lab-results') }}" class="nav-link py-2 {{ request()->routeIs('doctor.lab-results') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-pulse me-2"></i>Lab Reviews
                        @if(isset($pendingLabReviews) && $pendingLabReviews > 0)
                            <span class="badge bg-info ms-auto">{{ $pendingLabReviews }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="#" class="nav-link py-2">
                        <i class="bi bi-file-earmark-text me-2"></i>Medical Certificates
                        <span class="badge bg-secondary ms-auto">Soon</span>
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a href="#" class="nav-link py-2">
                        <i class="bi bi-prescription2 me-2"></i>Prescriptions
                        <span class="badge bg-secondary ms-auto">Soon</span>
                    </a>
                </li>
            </ul>
            
            <hr>
            
            <!-- Logout Button -->
            <a href="http://127.0.0.1:8000/logout" class="nav-link py-2 w-100 text-start text-white">
                <i class="bi bi-box-arrow-right me-2"></i>Sign out
            </a>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1" style="margin-left: 220px; width: calc(100% - 220px);">
            <div class="container-fluid h-100 p-2">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
