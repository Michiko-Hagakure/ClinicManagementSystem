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
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        html, body { overflow-x: hidden; }
    </style>
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Fixed Sidebar -->
        <div class="sidebar sidebar-custom d-flex flex-column flex-shrink-0 p-2 position-fixed" style="width: 260px; height: 100vh; z-index: 1000;">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-2 text-white text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" width="50" height="50" class="me-2">
                <span class="fs-5">EMR System</span>
            </a>

            <div class="text-center my-3">
                @php
                    // Get user data from session (check both formats)
                    $userName = session('user_name') ?? session('name', 'Name');
                    $userRole = session('user_role') ?? session('role', 'clinic staff');
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
            
            <div class="sidebar-menu" style="flex:1; overflow-y:auto; overflow-x:hidden;">
            <ul class="nav nav-pills flex-column mb-0">
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
            </div>
            <hr>
            
            <!-- Logout Button -->
            <a href="http://127.0.0.1:8000/logout" class="nav-link py-2 w-100 text-start text-white" onclick="return confirmLogout(event)">
                <i class="bi bi-box-arrow-right me-2"></i>Log out
            </a>
        </div>
        
        <!-- Main Content -->
        <main class="flex-grow-1" style="margin-left: 260px; box-sizing: border-box;">
            <div class="container-fluid h-100 p-2">
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