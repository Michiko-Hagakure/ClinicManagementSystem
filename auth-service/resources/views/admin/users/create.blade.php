@extends('layouts.admin')

@section('title', 'Create New User')
@section('page-title', 'Create New User')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>New User Account</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                    @csrf

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
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Profile Picture</label>
                            <div class="text-center">
                                <div class="profile-preview mb-3">
                                    <img id="profile-preview" src="" 
                                         alt="Profile Preview" 
                                         class="rounded-circle" 
                                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #00A689;">
                                </div>
                                <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*" class="d-none">
                                <input type="hidden" id="cropped_image" name="cropped_image">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="upload-btn">
                                    <i class="bi bi-cloud-upload me-1"></i>Upload Photo
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm d-none" id="remove-photo-btn">
                                    <i class="bi bi-trash me-1"></i>Remove
                                </button>
                                <div><small class="text-muted">Click to upload a profile picture</small></div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Employee ID:</strong> A unique 6-digit Employee ID will be automatically generated upon account creation. 
                                This ID will be used for login instead of email.
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            <small class="form-text text-muted">For communication purposes only (not used for login)</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                    <i class="bi bi-eye" id="passwordConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" {{ old('role') == $roleValue ? 'selected' : '' }}>
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
                                    <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Role Descriptions</h6>
                <hr>
                <div class="mb-3">
                    <strong>System Administrator</strong>
                    <p class="small text-muted mb-0">Full access to all system settings and user management</p>
                </div>
                <div class="mb-3">
                    <strong>Owner</strong>
                    <p class="small text-muted mb-0">Business owner with access to financial reports and settings</p>
                </div>
                <div class="mb-3">
                    <strong>Doctor</strong>
                    <p class="small text-muted mb-0">Access to EMR, patient consultations, and medical records</p>
                </div>
                <div class="mb-3">
                    <strong>Medical Staff</strong>
                    <p class="small text-muted mb-0">Nurses and medical assistants</p>
                </div>
                <div class="mb-3">
                    <strong>Cashier</strong>
                    <p class="small text-muted mb-0">Access to POS and billing system</p>
                </div>
                <div class="mb-3">
                    <strong>Pharmacist</strong>
                    <p class="small text-muted mb-0">Manage medicine inventory and dispensing</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cropModalLabel">
                    <i class="bi bi-crop me-2"></i>Crop Profile Picture
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <p class="text-muted">Drag and resize the circle to crop your profile picture</p>
                </div>
                <div class="img-container" style="max-height: 500px;">
                    <img id="image-to-crop" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="crop-button">
                    <i class="bi bi-check-circle me-1"></i>Crop & Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
let cropper;
const profilePreview = document.getElementById('profile-preview');
const profilePictureInput = document.getElementById('profile_picture_input');
const croppedImageInput = document.getElementById('cropped_image');
const uploadBtn = document.getElementById('upload-btn');
const removePhotoBtn = document.getElementById('remove-photo-btn');
const imageToCrop = document.getElementById('image-to-crop');
const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));

// Upload button click
uploadBtn.addEventListener('click', () => {
    profilePictureInput.click();
});

// File selected
profilePictureInput.addEventListener('change', (e) => {
    const files = e.target.files;
    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = (event) => {
            imageToCrop.src = event.target.result;
            cropModal.show();
            
            // Initialize Cropper after modal is shown
            document.getElementById('cropModal').addEventListener('shown.bs.modal', function () {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: false,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }, { once: true });
        };
        reader.readAsDataURL(files[0]);
    }
});

// Crop button click
document.getElementById('crop-button').addEventListener('click', () => {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        canvas.toBlob((blob) => {
            const reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = () => {
                const base64data = reader.result;
                croppedImageInput.value = base64data;
                profilePreview.src = base64data;
                removePhotoBtn.classList.remove('d-none');
                cropModal.hide();
                cropper.destroy();
            };
        });
    }
});

// Remove photo
removePhotoBtn.addEventListener('click', () => {
    // Use cache-busting parameter to prevent browser cache
    profilePreview.src = 'https://ui-avatars.com/api/?name=User&color=FFFFFF&background=00A689&size=200&bold=true&_=' + Date.now();
    croppedImageInput.value = '';
    profilePictureInput.value = '';
    removePhotoBtn.classList.add('d-none');
});

// Cleanup cropper when modal is hidden
document.getElementById('cropModal').addEventListener('hidden.bs.modal', function () {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
});

// Auto-scroll to error alert if present
document.addEventListener('DOMContentLoaded', function() {
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert) {
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Force reset profile preview to default on page load (prevent browser cache issues)
    const defaultAvatar = 'https://ui-avatars.com/api/?name=User&color=FFFFFF&background=00A689&size=200&bold=true&_=' + Date.now();
    profilePreview.src = defaultAvatar;
    croppedImageInput.value = '';
    profilePictureInput.value = '';
    removePhotoBtn.classList.add('d-none');
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

