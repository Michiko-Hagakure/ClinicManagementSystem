<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mary Angels Diagnostic Clinic - Pharmacy')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        html, body { overflow-x: hidden; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(to bottom, #00A689);
            padding: 16px 0 0 0;
            box-shadow: 2px 0 14px rgba(0,0,0,0.12);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .sidebar-menu { flex: 1; overflow-y: auto; overflow-x: hidden; padding-top: 8px; }
        .sidebar-menu::-webkit-scrollbar { width: 6px; }
        .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.35); border-radius: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5); }
        
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
            box-sizing: border-box;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.92) !important;
            padding: 10px 14px;
            border-radius: 10px;
            margin: 6px 12px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.12);
            color: #ffffff !important;
            transform: translateX(4px);
        }
        
        .brand-link { color: #e7fffb; text-decoration: none; padding: 0 16px; display: flex; align-items: center; }
        .brand-link span { font-weight: 600; font-size: 1.05rem; }
        .brand-sub { color: rgba(255,255,255,0.85); font-size: 0.9rem; padding: 0 16px; margin-top: 4px; }
        .sidebar-user { text-align: center; color: #ffffff; margin-bottom: 16px; }
        .sidebar-user .avatar { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .sidebar-user .role { color: rgba(255,255,255,0.85); font-size: 0.85rem; }

        .sidebar-footer { padding: 10px 16px 16px 16px; }
        .logout-btn {
            display: block;
            width: 100%;
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            text-align: left;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.18); }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table thead th {
            background-color: #34495e;
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px;
        }
        
        .table tbody td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .low-stock {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .out-of-stock {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        .in-stock {
            background-color: #d1e7dd;
            border-left: 4px solid #198754;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="{{ route('pharmacy.dashboard') }}" class="brand-link">
            <img src="http://127.0.0.1:8000/images/logo.png" alt="Clinic Logo" width="50" height="50" class="me-2">
            <span>Pharmacy System</span>
        </a>

        <div class="sidebar-user">
            @php
                $profilePicture = session('user_profile_picture', 'http://127.0.0.1:8001/images/avatar.jpg');
                // Convert localhost URLs to the correct auth service port
                if (str_contains($profilePicture, 'localhost')) {
                    $profilePicture = str_replace('http://localhost', 'http://127.0.0.1:8000', $profilePicture);
                }
                // If the profile picture is a relative path from auth service
                elseif (str_starts_with($profilePicture, '/uploads/')) {
                    $profilePicture = 'http://127.0.0.1:8000' . $profilePicture;
                }
            @endphp
            <img src="{{ $profilePicture }}" class="avatar mb-2" alt="User Avatar" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(session('user_name','User')) }}&background=00A689&color=fff'">
            <div class="fw-semibold">{{ session('user_name','Name') }}</div>
            <div class="role">{{ ucfirst(str_replace('_',' ', session('user_role','pharmacy staff'))) }}</div>
        </div>
        
        <div class="sidebar-menu">
        <ul class="nav nav-pills flex-column mb-0">
            <li class="nav-item">
                <a href="{{ route('pharmacy.dashboard') }}" class="nav-link {{ request()->routeIs('pharmacy.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('medicine.index') }}" class="nav-link {{ request()->routeIs('medicine.*') ? 'active' : '' }}">
                    <i class="bi bi-capsule me-2"></i>Manage Inventory
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pharmacy.history') }}" class="nav-link {{ request()->routeIs('pharmacy.history') ? 'active' : '' }}">
                    <i class="bi bi-clock-history me-2"></i>Dispensation History
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('inventory.reports') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up me-2"></i>Reports
                </a>
            </li>
            
        </ul>
        </div>

        <!-- Sidebar footer with logout -->
        <div class="sidebar-footer mt-auto">
            <a href="{{ route('logout') }}" class="logout-btn text-decoration-none" onclick="return confirmLogout(event)">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="h3 mb-0 text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-muted mb-0">@yield('page-description', 'Manage your pharmacy inventory efficiently')</p>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted" id="current-datetime"></span>
            </div>
        </div>
        
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Page Content -->
        @yield('content')
    </div>
    
    <!-- jQuery (needed for Select2 and other plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Real-time clock
        function updateDateTime() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            };
            const formattedDate = now.toLocaleString('en-US', options);
            document.getElementById('current-datetime').textContent = formattedDate;
        }
        
        // Update time immediately and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Update alert count badge
        function updateAlertCount() {
            fetch('/api/v1/inventory/low-stock-alerts')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('alert-count').textContent = data.count;
                        if (data.count > 0) {
                            document.getElementById('alert-count').classList.remove('bg-secondary');
                            document.getElementById('alert-count').classList.add('bg-danger');
                        } else {
                            document.getElementById('alert-count').classList.remove('bg-danger');
                            document.getElementById('alert-count').classList.add('bg-secondary');
                        }
                    }
                })
                .catch(error => console.log('Alert count update failed:', error));
        }
        
        // Update alert count on page load and every 30 seconds
        updateAlertCount();
        setInterval(updateAlertCount, 30000);
        
        // Logout confirmation
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
        
        // Confirmation dialogs for delete actions
        function confirmDelete(title, text, url) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit a form for DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>
