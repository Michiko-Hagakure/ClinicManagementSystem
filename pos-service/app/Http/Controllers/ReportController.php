<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\MedicalBill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Show financial summary report.
     */
    public function financial(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        
        // Convert to Carbon instances for easier manipulation
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        // Calculate date range for display
        $dateRange = $start->format('M d, Y');
        if (!$start->isSameDay($end)) {
            $dateRange .= ' - ' . $end->format('M d, Y');
        }
        
        // Total Revenue (All paid bills)
        $totalRevenue = MedicalBill::paid()
            ->whereBetween('paid_at', [$start, $end])
            ->sum('total_amount');
            
        // Transaction counts
        $totalTransactions = MedicalBill::paid()
            ->whereBetween('paid_at', [$start, $end])
            ->count();
            
        $pendingTransactions = MedicalBill::pending()
            ->whereBetween('created_at', [$start, $end])
            ->count();
            
        // Payment method breakdown
        $paymentMethods = MedicalBill::paid()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function($item) {
                return [
                    ucfirst($item->payment_method ?? 'cash') => [
                        'count' => $item->count,
                        'total' => $item->total
                    ]
                ];
            });
            
        // Hourly breakdown for today only
        $hourlyData = [];
        if ($start->isSameDay($end) && $start->isToday()) {
            $hourlyData = MedicalBill::paid()
                ->whereDate('paid_at', $start)
                ->selectRaw('HOUR(paid_at) as hour, COUNT(*) as transactions, SUM(total_amount) as revenue')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->keyBy('hour');
        }
        
        // Top services/items
        $topServices = MedicalBill::paid()
            ->join('bill_items', 'medical_bills.id', '=', 'bill_items.medical_bill_id')
            ->whereBetween('medical_bills.paid_at', [$start, $end])
            ->selectRaw('bill_items.service_name, COUNT(*) as quantity, SUM(bill_items.total_price) as revenue')
            ->groupBy('bill_items.service_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
            
        // Average transaction value
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Pending revenue (unpaid bills)
        $pendingRevenue = MedicalBill::pending()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        return view('reports.financial', compact(
            'dateRange',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'pendingTransactions',
            'pendingRevenue',
            'paymentMethods',
            'hourlyData',
            'topServices',
            'averageTransaction'
        ));
    }

    /**
     * Show daily reports.
     */
    public function daily(): View
    {
        return view('reports.daily');
    }

    /**
     * Show receipts reports.
     */
    public function receipts(): View
    {
        return view('reports.receipts');
    }
}
