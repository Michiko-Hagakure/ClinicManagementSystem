@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit User: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-person-badge me-2"></i>
                                <strong>Employee ID:</strong> <code class="fs-5">{{ $user->employee_id ?? 'Not Assigned' }}</code>
                                <small class="d-block text-muted mt-1">This Employee ID is used for login</small>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="bi bi-eye" id="passwordConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                @foreach($roles as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" {{ old('role', $user->role) == $roleValue ? 'selected' : '' }}>
                                        {{ $roleLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" name="department">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $user->department) == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Account Created:</strong> {{ $user->created_at->format('F d, Y \a\t g:i A') }}
                        @if($user->updated_at != $user->created_at)
                            <br><strong>Last Updated:</strong> {{ $user->updated_at->format('F d, Y \a\t g:i A') }}
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-warning bg-opacity-10 border-warning">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-exclamation-triangle me-2"></i>Important Notes</h6>
                <hr>
                <ul class="small mb-0">
                    <li>Changing the email will update the login credentials</li>
                    <li>Password field is optional - leave blank to keep current password</li>
                    <li>Changing role may affect user access permissions</li>
                    <li>Deactivating an account will prevent user from logging in</li>
                    @if($user->id === auth()->id())
                        <li class="text-danger mt-2"><strong>You are editing your own account!</strong></li>
                    @endif
                </ul>
            </div>
        </div>

        @if($user->id !== auth()->id())
            <div class="card border-danger mt-3">
                <div class="card-body">
                    <h6 class="card-title text-danger"><i class="bi bi-trash me-2"></i>Danger Zone</h6>
                    <hr>
                    <p class="small mb-3">Permanently delete this user account. This action cannot be undone.</p>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                          onsubmit="return confirm('Are you absolutely sure you want to delete {{ $user->name }}? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-trash me-1"></i>Delete User Account
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-scroll to error alert if present
document.addEventListener('DOMContentLoaded', function() {
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert) {
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Password visibility toggle
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');
const passwordIcon = document.getElementById('passwordIcon');

togglePassword.addEventListener('click', function () {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    if (type === 'text') {
        passwordIcon.classList.remove('bi-eye');
        passwordIcon.classList.add('bi-eye-slash');
    } else {
        passwordIcon.classList.remove('bi-eye-slash');
        passwordIcon.classList.add('bi-eye');
    }
});

// Password confirmation visibility toggle
const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
const passwordConfirm = document.getElementById('password_confirmation');
const passwordConfirmIcon = document.getElementById('passwordConfirmIcon');

togglePasswordConfirm.addEventListener('click', function () {
    const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordConfirm.setAttribute('type', type);
    
    if (type === 'text') {
        passwordConfirmIcon.classList.remove('bi-eye');
        passwordConfirmIcon.classList.add('bi-eye-slash');
    } else {
        passwordConfirmIcon.classList.remove('bi-eye-slash');
        passwordConfirmIcon.classList.add('bi-eye');
    }
});
</script>
@endsection

