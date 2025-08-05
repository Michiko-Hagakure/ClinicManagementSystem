<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmrController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/emr', [EmrController::class, 'index'])->name('emr.index');

Route::get('/emr/patient/{patient}', [EmrController::class, 'show'])->name('emr.show');

Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');

Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');

Route::get('/login', [LoginController::class, 'create'])->name('login');

Route::post('/login', [LoginController::class, 'store']);