<?php

use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReportsApiController;
use Illuminate\Support\Facades\Route;

// Debug route - TEMPORARY
Route::get('/debug-session', function() {
    return response()->json([
        'all_session' => session()->all(),
        'user_name' => session('user_name'),
        'user_role' => session('user_role'),
        'user_profile_picture' => session('user_profile_picture'),
        'user_department' => session('user_department'),
    ]);
});

// Force refresh session from Auth service - TEMPORARY
Route::get('/refresh-session', function() {
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Cookie' => 'laravel_session=' . request()->cookie('laravel_session')
        ])->get('http://127.0.0.1:8000/api/auth/check');
        
        if ($response->successful() && $response->json('authenticated')) {
            $user = $response->json('user');
            
            // Get the full user data including profile picture
            $userResponse = \Illuminate\Support\Facades\Http::get('http://127.0.0.1:8000/api/users/' . $user['id']);
            
            if ($userResponse->successful()) {
                $fullUser = $userResponse->json();
                session(['user_profile_picture' => $fullUser['profile_picture_url'] ?? null]);
                session(['user_department' => $fullUser['department'] ?? null]);
                session(['user_name' => $fullUser['name']]);
                session(['user_role' => $fullUser['role']]);
                
                return redirect('/')->with('success', 'Session refreshed! Your profile picture should now appear.');
            }
        }
        
        return 'Could not refresh session. Please log out and log in again.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// All authenticated routes
Route::middleware(['auth.service'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Patient routes
    Route::resource('patients', PatientController::class);
    Route::get('/patients/{patient}/consultations', [PatientController::class, 'consultations'])->name('patients.consultations');
    Route::get('/patients/{patient}/lab-results', [PatientController::class, 'labResults'])->name('patients.lab-results');

    // Consultation routes
    Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('/consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
    Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');

    // Lab Records routes
    Route::get('/lab-records', [LabResultController::class, 'index'])->name('lab-records.index');
    Route::get('/lab-records/create', [LabResultController::class, 'create'])->name('lab-records.create');
    Route::post('/lab-records', [LabResultController::class, 'store'])->name('lab-records.store');
    Route::get('/lab-records/by-department/{department}', [LabResultController::class, 'byDepartment'])->name('lab-records.by-department');
    Route::get('/lab-records/{lab_record}', [LabResultController::class, 'show'])->name('lab-records.show');
    Route::get('/lab-records/{lab_record}/edit', [LabResultController::class, 'edit'])->name('lab-records.edit');
    Route::put('/lab-records/{lab_record}', [LabResultController::class, 'update'])->name('lab-records.update');
    Route::delete('/lab-records/{lab_record}', [LabResultController::class, 'destroy'])->name('lab-records.destroy');

    // Doctor Routes
Route::prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/patient-queue', [DoctorController::class, 'patientQueue'])->name('patient-queue');
        Route::get('/patient-records', [DoctorController::class, 'patientRecords'])->name('patient-records');
        Route::get('/my-consultations', [DoctorController::class, 'myConsultations'])->name('my-consultations');
        Route::get('/lab-results', [DoctorController::class, 'labResults'])->name('lab-results');
        Route::post('/lab-results/upload', [DoctorController::class, 'uploadLabResult'])->name('lab-results.upload');
        Route::post('/lab-results/{lab_result}/review', [DoctorController::class, 'reviewLabResult'])->name('lab-results.review');
        Route::get('/patients/{patient}', [DoctorController::class, 'viewPatient'])->name('view-patient');
        
        // Consultation routes
        Route::get('/consultation/{patient}', [DoctorController::class, 'consultation'])->name('consultation');
        Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}/edit', [DoctorController::class, 'editConsultation'])->name('edit-consultation');
        Route::put('/consultations/{consultation}', [DoctorController::class, 'updateConsultation'])->name('update-consultation');
        Route::put('/consultation/{consultation}/update', [DoctorController::class, 'updateConsultation'])->name('consultation.update');
        Route::post('/consultations/{consultation}/lab-result', [DoctorController::class, 'uploadLabResult'])->name('upload-lab-result');
    });
});

// Public API Routes for Owner Dashboard (no auth middleware required)
Route::prefix('api')->group(function () {
    Route::get('/reports/patients', [ReportsApiController::class, 'getPatientReport']);
});
