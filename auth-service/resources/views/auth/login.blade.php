<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clinic Management System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00A689 0%, #007a6b 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .login-left {
            background: linear-gradient(45deg, #00A689 0%, #007a6b 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        .login-right {
            padding: 3rem;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #00A689;
            box-shadow: 0 0 0 0.2rem rgba(0, 166, 137, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #00A689 0%, #007a6b 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            color: #000000;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 166, 137, 0.3);
            color: #000000;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
        .role-indicator {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }
        .loading-spinner {
            display: none;
        }
        .loading .loading-spinner {
            display: inline-block;
        }
        .loading .btn-text {
            display: none;
        }
        .alert {
            border-radius: 12px;
            border: none;
        }
        .form-label-aligned {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card" style="max-width: 500px;">
            <div class="login-right" style="text-align: center;">
                <!-- Logo -->
                <div class="mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Clinic Logo" class="mx-auto d-block mb-3" style="width: 100px; height: 100px; border-radius: 50%;">
                </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif

                    <!-- Success Messages -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                        @csrf
                        
                        <!-- Employee ID Input -->
                        <div class="row mb-3 justify-content-center">
                            <label for="employee_id" class="col-4 text-end fw-semibold form-label-aligned">
                                <span><i class="bi bi-person-badge me-2 text-primary"></i>Employee ID</span>
                            </label>
                            <div class="col-7">
                                <input type="text" 
                                       class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" 
                                       name="employee_id" 
                                       value="{{ old('employee_id') }}" 
                                       required 
                                       maxlength="6"
                                       pattern="[0-9]{6}"
                                       autocomplete="off" 
                                       placeholder="123456">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Example: 123456</small>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="row mb-4 justify-content-center">
                            <label for="password" class="col-4 text-end fw-semibold form-label-aligned">
                                <span><i class="bi bi-lock me-2 text-primary"></i>Password</span>
                            </label>
                            <div class="col-7">
                                <div class="position-relative">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
                                    <button type="button" class="password-toggle" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row justify-content-center">
                            <div class="col-7 offset-4">
                                <button type="submit" class="btn btn-primary btn-login w-100">
                                    <span class="loading-spinner spinner-border spinner-border-sm me-2"></span>
                                    <span class="btn-text">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }

        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.classList.add('loading');
            submitButton.disabled = true;
        });

        // Auto-focus first input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('employee_id').focus();
        });
    </script>
</body>
</html>
