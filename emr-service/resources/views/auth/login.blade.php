<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Mary Angels Diagnostic Clinic</title>
    
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            background: linear-gradient(135deg, #00A689 0%, #004d40 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #00A689, #00897b);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255,255,255,0.05) 10px,
                rgba(255,255,255,0.05) 20px
            );
            animation: move 20s linear infinite;
        }
        
        @keyframes move {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .clinic-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }
        
        .login-form {
            padding: 3rem 2rem;
        }
        
        .form-control {
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            background: #f8fffe;
        }
        
        .form-control:focus {
            border-color: #00A689;
            box-shadow: 0 0 0 0.2rem rgba(0, 166, 137, 0.25);
            background: white;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #00A689, #00897b);
            border: none;
            border-radius: 12px;
            padding: 0.8rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 166, 137, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 166, 137, 0.4);
            background: linear-gradient(135deg, #00897b, #00695c);
        }
        
        .role-indicator {
            background: #e8f5e8;
            border-left: 4px solid #4caf50;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin-bottom: 1.5rem;
        }
        
        .floating-icons {
            position: absolute;
            top: 50%;
            right: 10%;
            transform: translateY(-50%);
            opacity: 0.1;
            font-size: 10rem;
            color: white;
            z-index: 1;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
        }
        
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .login-footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header Section -->
        <div class="login-header">
            <div class="clinic-logo">
                <i class="bi bi-hospital" style="font-size: 2rem; color: #00A689;"></i>
            </div>
            <h2 class="mb-2" style="position: relative; z-index: 2;">Mary Angels Diagnostic Clinic</h2>
            <p class="mb-0" style="position: relative; z-index: 2; opacity: 0.9;">Healthcare Management System</p>
            
            <!-- Floating Medical Icons -->
            <div class="floating-icons">
                <i class="bi bi-heart-pulse"></i>
            </div>
        </div>
        
        <!-- Login Form -->
        <div class="login-form">
            <!-- Role Information -->
            <div class="role-indicator">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle text-success me-2"></i>
                    <div>
                        <strong>Multi-Role Access</strong>
                        <div class="small text-muted">
                            🩺 Doctors • 👥 Clinic Staff • 💰 Cashiers
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            
            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>Email Address
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="Enter your clinic email address">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <div class="position-relative">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required 
                               placeholder="Enter your password">
                        <button type="button" 
                                class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3" 
                                onclick="togglePassword()" 
                                style="border: none; background: none; z-index: 10;">
                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-login btn-lg" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        <span id="loginBtnText">Sign In to Dashboard</span>
                        <div class="spinner-border spinner-border-sm ms-2 d-none" id="loginSpinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="login-footer">
            <div class="row">
                <div class="col-md-4 text-center mb-2">
                    <i class="bi bi-person-badge text-primary"></i>
                    <div class="small text-muted">Doctors</div>
                    <div class="small">Medical Portal</div>
                </div>
                <div class="col-md-4 text-center mb-2">
                    <i class="bi bi-hospital text-info"></i>
                    <div class="small text-muted">Clinic Staff</div>
                    <div class="small">EMR System</div>
                </div>
                <div class="col-md-4 text-center mb-2">
                    <i class="bi bi-cash-register text-success"></i>
                    <div class="small text-muted">Cashiers</div>
                    <div class="small">POS System</div>
                </div>
            </div>
            <hr class="my-3">
            <p class="text-muted mb-0 small">
                © {{ date('Y') }} Mary Angels Diagnostic Clinic. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
        
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginSpinner = document.getElementById('loginSpinner');
            
            loginBtn.disabled = true;
            loginBtnText.textContent = 'Signing in...';
            loginSpinner.classList.remove('d-none');
        });
        
        // Auto-focus email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>
