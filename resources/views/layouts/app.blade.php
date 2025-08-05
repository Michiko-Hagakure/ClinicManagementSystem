<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clinic Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Style -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            height: 100vh;
            background: linear-gradient(to bottom, #007bff, #0056b3);
            color: white;
        }

        .sidebar .nav-link {
            color: white;
            transition: background 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .main-content {
            margin-left: 16.666667%;
        }

        @media (max-width:992px) {
            .main-content {
                margin-left: 25%;
            }
        }

        .header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .card-hover:hover {
            transform: translateY(-3px);
            transition: all 0.2s ease-in-out;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <div class="mb-4 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 120px;">
            </div>
            <nav class="nav flex-column">
                <a href="{{ url('/dashboard') }}" @class(['nav-link', 'active' => request()->is('dashboard')])>Dashboard</a>
                <a href="{{ route('emr.index') }}" @class(['nav-link', 'active' => request()->is('emr*')])>EMR</a>
                <a href="{{ route('billing.index') }}" @class(['nav-link', 'active' => request()->is('billing*')])>Billing</a>
                <a href="{{ route('pharmacy.index') }}" @class(['nav-link', 'active' => request()->is('pharmacy*')])>Pharmacy</a>
                <a href="#" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">Reports</a>
                <a href="#" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">Settings</a>
            </nav>
        </div>

        <!-- Main -->
        <div class="col-md-9 col-lg-10 p-4 main-content">

            @yield('content')>

        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
