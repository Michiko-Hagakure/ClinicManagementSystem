<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the cashier dashboard with daily stats and quick actions.
     */
    public function index(): View
    {
        // Mock data for now - will be replaced with actual database queries
        $todayRevenue = 15750.00;
        $todayTransactions = 23;
        $pendingBills = 5;
        $medicineRevenue = 3250.00;
        
        // Recent transactions (mock data)
        $recentTransactions = collect([
            (object) [
                'created_at' => now()->subMinutes(15),
                'patient_name' => 'Maria Santos',
                'services' => 'X-ray, Consultation',
                'total_amount' => 850.00
            ],
            (object) [
                'created_at' => now()->subMinutes(30),
                'patient_name' => 'Juan Dela Cruz',
                'services' => 'Laboratory, ECG',
                'total_amount' => 650.00
            ],
            (object) [
                'created_at' => now()->subHour(),
                'patient_name' => 'Ana Rodriguez',
                'services' => 'Ultrasound',
                'total_amount' => 1200.00
            ]
        ]);
        
        // Pending actions (mock data)
        $pendingActions = collect([
            (object) [
                'patient_name' => 'Pedro Garcia',
                'service_type' => 'Post-consultation billing'
            ],
            (object) [
                'patient_name' => 'Lisa Morales',
                'service_type' => 'Medicine prescription'
            ]
        ]);
        
        return view('dashboard', compact(
            'todayRevenue',
            'todayTransactions', 
            'pendingBills',
            'medicineRevenue',
            'recentTransactions',
            'pendingActions'
        ));
    }
}