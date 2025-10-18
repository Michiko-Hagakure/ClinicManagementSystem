<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes - Protected by admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');
});

// Owner routes - Protected by owner middleware
Route::middleware(['auth', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/emr', [OwnerController::class, 'emrReports'])->name('reports.emr');
    Route::get('/reports/pos', [OwnerController::class, 'posReports'])->name('reports.pos');
    Route::get('/reports/inventory', [OwnerController::class, 'inventoryReports'])->name('reports.inventory');
    Route::get('/reports/export', [OwnerController::class, 'exportPDF'])->name('reports.export');
});

// API routes for inter-service authentication checks
// These routes need session handling but no authentication middleware
// The controller manually checks Auth::check() which uses the session
Route::middleware('web')->group(function () {
    Route::get('/api/auth/check', [AuthController::class, 'checkAuth']);
    Route::get('/api/auth/token', [AuthController::class, 'getToken']);
});

// Public API for getting user data (used by other services)
Route::get('/api/users/{id}', function($id) {
    $user = \App\Models\User::find($id);
    
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'department' => $user->department,
        'profile_picture_url' => $user->profile_picture_url,
        'is_active' => $user->is_active,
    ]);
});

// Public API for getting doctors list (used by POS service)
Route::get('/api/doctors', function() {
    $doctors = \App\Models\User::where('role', 'doctor')
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'employee_id', 'department']);
    
    return response()->json($doctors);
});
