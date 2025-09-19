<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// API route for other services to check authentication
Route::get('/auth/check', [AuthController::class, 'checkAuth'])->name('auth.check');
