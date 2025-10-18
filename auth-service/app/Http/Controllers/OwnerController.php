<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class OwnerController extends Controller
{
    /**
     * Display the Owner Dashboard with consolidated reports
     */
    public function dashboard(Request $request)
    {
        // Get date range from request or default to current month
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        
        // Get filter preset if any
        $preset = $request->input('preset', 'month');

        // Fetch data from all services
        $financialData = $this->getFinancialData($startDate, $endDate);
        $patientData = $this->getPatientData($startDate, $endDate);
        $inventoryData = $this->getInventoryData($startDate, $endDate);

        return view('owner.dashboard', compact(
            'financialData',
            'patientData',
            'inventoryData',
            'startDate',
            'endDate',
            'preset'
        ));
    }

    /**
     * Get financial data from POS Service
     */
    private function getFinancialData($startDate, $endDate)
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:8002/api/reports/financial', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch financial data: ' . $e->getMessage());
        }

        return [
            'total_revenue' => 0,
            'total_transactions' => 0,
            'total_bills' => 0,
            'paid_bills' => 0,
            'unpaid_bills' => 0,
            'payment_methods' => [],
            'daily_revenue' => [],
            'revenue_trends' => [],
        ];
    }

    /**
     * Get patient statistics from EMR Service
     */
    private function getPatientData($startDate, $endDate)
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:8001/api/reports/patients', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch patient data: ' . $e->getMessage());
        }

        return [
            'total_patients' => 0,
            'new_patients' => 0,
            'total_consultations' => 0,
            'services_rendered' => [],
            'daily_visits' => [],
        ];
    }

    /**
     * Get inventory data from Inventory Service
     */
    private function getInventoryData($startDate, $endDate)
    {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:8003/api/reports/inventory', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch inventory data: ' . $e->getMessage());
        }

        return [
            'total_medicines' => 0,
            'total_items' => 0,
            'total_stock_value' => 0,
            'low_stock_items' => 0,
            'expiring_items' => 0,
            'expiring_soon' => 0,
            'top_selling' => [],
            'top_selling_medicines' => [],
            'stock_value' => 0,
        ];
    }

    /**
     * Display EMR Reports
     */
    public function emrReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        $preset = $request->input('preset', 'month');

        $patientData = $this->getPatientData($startDate, $endDate);

        return view('owner.emr-reports', compact('patientData', 'startDate', 'endDate', 'preset'));
    }

    /**
     * Display POS Reports
     */
    public function posReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        $preset = $request->input('preset', 'month');

        $financialData = $this->getFinancialData($startDate, $endDate);

        return view('owner.pos-reports', compact('financialData', 'startDate', 'endDate', 'preset'));
    }

    /**
     * Display Inventory Reports
     */
    public function inventoryReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());
        $preset = $request->input('preset', 'month');

        $inventoryData = $this->getInventoryData($startDate, $endDate);

        return view('owner.inventory-reports', compact('inventoryData', 'startDate', 'endDate', 'preset'));
    }

    /**
     * Export consolidated report as printable HTML (can be saved as PDF via browser)
     */
    public function exportPDF(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $financialData = $this->getFinancialData($startDate, $endDate);
        $patientData = $this->getPatientData($startDate, $endDate);
        $inventoryData = $this->getInventoryData($startDate, $endDate);

        $period = \Carbon\Carbon::parse($startDate)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('M d, Y');

        return view('owner.export-report', compact(
            'period',
            'financialData',
            'patientData',
            'inventoryData'
        ));
    }
}

