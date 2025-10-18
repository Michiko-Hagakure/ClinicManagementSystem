<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\LabResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $todayConsultations = Consultation::whereDate('created_at', today())->count();
        $pendingLabResults = LabResult::count();
        $monthlyConsultations = Consultation::whereMonth('created_at', now()->month)->count();
        
        // Recent patients
        $recentPatients = Patient::orderBy('created_at', 'desc')->take(5)->get();
        
        // Recent consultations
        $recentConsultations = Consultation::with('patient')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard', compact(
            'totalPatients',
            'todayConsultations',
            'pendingLabResults',
            'monthlyConsultations',
            'recentPatients',
            'recentConsultations'
        ));
    }
}

