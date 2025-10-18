<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mary Angels Diagnostic Clinic - Pharmacy System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
        }
        
        .logo i {
            font-size: 2.5rem;
            color: white;
        }
        
        .welcome-title {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .welcome-subtitle {
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .login-btn {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .login-btn:hover {
            background: linear-gradient(45deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        .features {
            margin-top: 3rem;
            text-align: left;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: #34495e;
        }
        
        .feature-item i {
            color: #3498db;
            margin-right: 15px;
            font-size: 1.2rem;
            width: 20px;
        }
        
        .system-info {
            background: rgba(52, 152, 219, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            border-left: 4px solid #3498db;
        }
        
        .system-info h6 {
            color: #2980b9;
            margin-bottom: 0.5rem;
        }
        
        .system-info small {
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="logo">
            <i class="bi bi-capsule-pill"></i>
        </div>
        
        <h1 class="welcome-title">Mary Angels Diagnostic Clinic</h1>
        <p class="welcome-subtitle">Pharmacy & Inventory Management System</p>
        
        <a href="http://127.0.0.1:8003/login" class="login-btn">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Sign In to Continue
        </a>
        
        <div class="features">
            <h5 class="mb-3" style="color: #2c3e50;">System Features</h5>
            <div class="feature-item">
                <i class="bi bi-speedometer2"></i>
                <span>Real-time Inventory Dashboard</span>
            </div>
            <div class="feature-item">
                <i class="bi bi-prescription2"></i>
                <span>Medicine Dispensing Workflow</span>
            </div>
            <div class="feature-item">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Automatic Low Stock Alerts</span>
            </div>
            <div class="feature-item">
                <i class="bi bi-graph-up"></i>
                <span>Comprehensive Reports & Analytics</span>
            </div>
            <div class="feature-item">
                <i class="bi bi-search"></i>
                <span>Advanced Medicine Search</span>
            </div>
        </div>
        
        <div class="system-info">
            <h6><i class="bi bi-info-circle me-2"></i>Access Information</h6>
            <small>
                <strong>Pharmacy Staff:</strong> Full medicine management and dispensing<br>
                <strong>Clinic Owner:</strong> Access to all features including reports<br>
                <strong>System:</strong> Integrated with EMR and POS services
            </small>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Secure authentication required • Session-based access control
            </small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
