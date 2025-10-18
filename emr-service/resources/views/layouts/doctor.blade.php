<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Doctor Portal - EMR System')</title>
    
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        html, body { overflow-x: hidden; }
    </style>
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
                @php
                    // Get user data from session (check both formats)
                    $userName = session('user_name') ?? session('name', 'Dr. Name');
                    $userRole = session('user_role') ?? session('role', 'doctor');
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
                <img src="{{ $profilePicture }}" alt="Doctor Avatar" 
                    class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                <h6 class="text-white mb-0">{{ $userName }}</h6>
                <small class="text-white-50">{{ $userRole === 'doctor' ? 'Physician' : ucfirst(str_replace('_', ' ', $userRole)) }}</small>
            </div>

            <hr>
            
            <div class="sidebar-menu" style="flex:1; overflow-y:auto; overflow-x:hidden;">
            <ul class="nav nav-pills flex-column mb-0">
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.dashboard') }}" class="nav-link py-2 {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.patient-queue') }}" class="nav-link py-2 {{ request()->routeIs('doctor.patient-queue') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i>Patient Queue
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.patient-records') }}" class="nav-link py-2 {{ request()->routeIs('doctor.patient-records') ? 'active' : '' }}">
                        <i class="bi bi-people-fill me-2"></i>Patient Records
                    </a>
                </li>
            
                <li class="nav-item mb-1">
                    <a href="{{ route('doctor.my-consultations') }}" class="nav-link py-2 {{ request()->routeIs('doctor.my-consultations') ? 'active' : '' }}">
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

            </ul>
            </div>
            <div class="p-2">
                <a href="http://127.0.0.1:8000/logout" class="nav-link py-2 text-white" onclick="return confirmLogout(event)">
                    <i class="bi bi-box-arrow-right me-2"></i>Log out
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1" style="margin-left: 220px; box-sizing: border-box; overflow-x: hidden;">
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
</body>
</html>
