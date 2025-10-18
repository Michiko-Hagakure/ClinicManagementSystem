<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportsApiController;
use App\Services\AuthService;

// Default route - redirect to pharmacy dashboard
Route::get('/', function () {
    return redirect()->route('pharmacy.dashboard');
})->middleware('auth.inventory');

// Add a simple welcome/login page for unauthenticated users
Route::get('/welcome', function () {
    return view('auth.welcome');
})->name('welcome');

// Logout: clear local session then redirect to central auth logout
Route::get('/logout', function (AuthService $authService) {
    $authService->clearUserSession();
    return redirect()->away($authService->getLogoutUrl());
})->name('logout');

// Pharmacy Staff Routes - Protected by authentication
// Pharmacy staff focus on INVENTORY MANAGEMENT only (medicine sales handled by cashiers)
Route::middleware('auth.inventory:pharmacy')->group(function () {
    Route::prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/dashboard', [PharmacyController::class, 'dashboard'])->name('dashboard');
        Route::get('/alerts', [PharmacyController::class, 'lowStockAlerts'])->name('alerts');
        Route::delete('/alerts/{alert}', [PharmacyController::class, 'dismissAlert'])->name('alerts.dismiss');
        Route::get('/history', [PharmacyController::class, 'dispensationHistory'])->name('history');
        Route::get('/medicine/{medicineId}/details', [PharmacyController::class, 'getMedicineDetails'])->name('medicine.details');
    });

    // Medicine Inventory Management (CRUD) - Core pharmacy staff responsibility
    Route::resource('medicine', MedicineController::class);
    Route::post('/medicine/{medicine}/update-stock', [MedicineController::class, 'updateStock'])->name('medicine.update-stock');
    
    // Reports - Accessible by pharmacy staff
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/reports', [InventoryController::class, 'reports'])->name('reports');
        Route::get('/low-stock', [InventoryController::class, 'lowStockReport'])->name('low-stock');
        Route::get('/dispensation-report', [InventoryController::class, 'dispensationReport'])->name('dispensation-report');
    });
});

// Inventory Management Routes - Admin only (Owner)
Route::middleware('auth.inventory:admin')->group(function () {
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/export/{type}', [InventoryController::class, 'exportData'])->name('export');
    });
});

// Public API Routes for inter-service communication (POS, EMR, etc.)
// These routes are for server-to-server communication without user authentication
Route::prefix('api/v1/public')->name('api.public.')->group(function () {
    Route::get('/medicines/search', [MedicineController::class, 'apiSearch'])->name('medicines.search');
    Route::get('/medicines/{medicineId}', [MedicineController::class, 'apiShow'])->name('medicines.show');
    Route::post('/medicines/{medicineId}/reduce-stock', [MedicineController::class, 'apiReduceStock'])->name('medicines.reduce-stock');
});

// Authenticated API Routes (for web interface making AJAX calls)
Route::prefix('api/v1')->name('api.')->middleware('auth.inventory:basic')->group(function () {
    Route::get('/medicines/search', [MedicineController::class, 'apiSearch'])->name('medicines.search');
    Route::get('/medicines/{medicineId}', [MedicineController::class, 'apiShow'])->name('medicines.show');
    Route::post('/medicines/{medicineId}/reduce-stock', [MedicineController::class, 'apiReduceStock'])->name('medicines.reduce-stock');
    Route::get('/inventory/low-stock-alerts', [InventoryController::class, 'apiLowStockAlerts'])->name('inventory.alerts');
});

// Public API Routes for Owner Dashboard (no auth middleware required)
Route::prefix('api')->group(function () {
    Route::get('/reports/inventory', [ReportsApiController::class, 'getInventoryReport']);
});
