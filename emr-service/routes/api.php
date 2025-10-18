<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LabResultController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Inter-service Communication API Routes (NO AUTH REQUIRED)
Route::prefix('v1')->group(function () {
    // Patient endpoints
    Route::get('patients', function (Request $request) {
        if ($request->has('q')) {
            return app(PatientController::class)->search($request);
        }
        return app(PatientController::class)->index($request);
    })->name('api.patients.search_or_index');
    
    Route::apiResource('patients', PatientController::class)->except(['index']);
    
    // Consultation endpoints  
    Route::get('consultations/find-for-assignment', [ConsultationController::class, 'findForAssignment'])->name('api.consultations.find-for-assignment');
    Route::patch('consultations/{consultation}/assign-doctor', [ConsultationController::class, 'assignDoctor'])->name('api.consultations.assign-doctor');
    Route::get('consultations/check', [ConsultationController::class, 'checkExists'])->name('api.consultations.check');
    Route::apiResource('consultations', ConsultationController::class);
    
    // Lab Results endpoints
    Route::apiResource('lab-results', LabResultController::class);
    
    // Additional search endpoints for compatibility
    Route::get('patients/search', [PatientController::class, 'search'])->name('api.patients.search');
    Route::get('consultations/search', [ConsultationController::class, 'search'])->name('api.consultations.search');
});

// Legacy route for backward compatibility
Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
