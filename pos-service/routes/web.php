<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportsApiController;
use App\Http\Middleware\CheckAuthService;

// Redirect root to auth service
Route::get('/', function () {
    return redirect()->away('http://127.0.0.1:8000/login');
});

// Receipt route - standalone (no authentication, no layout)
Route::get('/transactions/{receipt_number}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');

// All POS routes require authentication through auth service
Route::middleware(CheckAuthService::class)->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Transaction Routes
Route::resource('transactions', TransactionController::class);

// Billing Routes
Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
Route::get('/billing/process/{patient?}', [BillingController::class, 'process'])->name('billing.process');
Route::post('/billing/process', [BillingController::class, 'store'])->name('billing.store');

// Pharmacy Routes
Route::get('/pharmacy/sales', [PharmacyController::class, 'sales'])->name('pharmacy.sales');
Route::get('/pharmacy/search', [PharmacyController::class, 'search'])->name('pharmacy.search');
Route::get('/pharmacy/history', [PharmacyController::class, 'history'])->name('pharmacy.history');
Route::post('/pharmacy/sell', [PharmacyController::class, 'sell'])->name('pharmacy.sell');

// Report Routes
Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
Route::get('/reports/receipts', [ReportController::class, 'receipts'])->name('reports.receipts');

// Patient Routes
Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

// API Routes for Inter-service Communication
Route::prefix('api/v1')->group(function () {
    Route::get('transactions', [TransactionController::class, 'apiIndex']);
    Route::post('transactions', [TransactionController::class, 'apiStore']);
    Route::get('patients/search/{query}', [PatientController::class, 'apiSearch']);
    });
    
// Additional API Routes (for AJAX calls from views)
Route::prefix('api')->group(function () {
    Route::get('/patients/search', [PatientController::class, 'search'])->name('api.patients.search');
});

}); // End of authenticated routes

// Public API Routes for Owner Dashboard (no auth middleware required)
Route::prefix('api')->group(function () {
    Route::get('/reports/financial', [ReportsApiController::class, 'getFinancialReport']);
});