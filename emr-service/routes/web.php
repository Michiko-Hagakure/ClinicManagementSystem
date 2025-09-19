<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\DoctorController;
use App\Http\Middleware\CheckAuthService;

// Redirect root to auth service
Route::get('/', function () {
    return redirect()->away('http://127.0.0.1:8000/login');
});

// All routes require authentication through auth service
Route::middleware(CheckAuthService::class)->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
    $totalPatients = \App\Models\Patient::count();
    $todayConsultations = \App\Models\Consultation::whereDate('consultation_date', today())->count();
    $pendingLabResults = \App\Models\LabResult::count();
    $monthlyConsultations = \App\Models\Consultation::whereMonth('consultation_date', now()->month)->count();
    
    $recentPatients = \App\Models\Patient::orderBy('id', 'desc')->limit(5)->get();
    $recentConsultations = \App\Models\Consultation::with('patient')->latest('consultation_date')->limit(5)->get();
    
    return view('dashboard', compact(
        'totalPatients', 
        'todayConsultations', 
        'pendingLabResults', 
        'monthlyConsultations',
        'recentPatients',
        'recentConsultations'
    ));
    })->name('dashboard');

    // Patient Routes
Route::resource('patients', PatientController::class);
Route::get('patients/{patient}/consultations', [PatientController::class, 'consultations'])->name('patients.consultations');
Route::get('patients/{patient}/lab-results', [PatientController::class, 'labResults'])->name('patients.lab-results');

// Consultation Routes  
Route::resource('consultations', ConsultationController::class);
Route::get('consultations/search', [ConsultationController::class, 'search'])->name('consultations.search');
Route::get('consultations/{consultation}/lab-results', [ConsultationController::class, 'labResults'])->name('consultations.lab-results');

// Lab Results Routes
Route::resource('lab-records', LabResultController::class);
Route::post('lab-records/{labResult}/upload', [LabResultController::class, 'uploadFile'])->name('lab-records.upload');
Route::post('lab-records/{labResult}/mark-reviewed', [LabResultController::class, 'markAsReviewed'])->name('lab-records.mark-reviewed');
Route::post('lab-records/{labResult}/add-notes', [LabResultController::class, 'addDoctorNotes'])->name('lab-records.add-notes');
Route::get('lab-records/pending/review', [LabResultController::class, 'pendingReview'])->name('lab-records.pending-review');
Route::get('lab-records/department/{dept?}', [LabResultController::class, 'byDepartment'])->name('lab-records.by-department');

// Doctor Interface Routes
Route::prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/', [DoctorController::class, 'dashboard'])->name('dashboard');
    Route::get('patient-queue', [DoctorController::class, 'patientQueue'])->name('patient-queue');
    Route::get('consultation/{patient}', [DoctorController::class, 'consultation'])->name('consultation');
    Route::put('consultation/{consultation}', [DoctorController::class, 'updateConsultation'])->name('consultation.update');
    Route::get('lab-results', [DoctorController::class, 'labResults'])->name('lab-results');
    Route::put('lab-results/{labResult}/review', [DoctorController::class, 'reviewLabResult'])->name('lab-results.review');
});

}); // End of authenticated routes
