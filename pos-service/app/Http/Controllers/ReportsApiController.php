<?php

namespace App\Http\Controllers;

use App\Models\MedicalBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsApiController extends Controller
{
    /**
     * Get financial reports for owner dashboard
     */
    public function getFinancialReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfDay());

        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Total Revenue
        $totalRevenue = MedicalBill::whereBetween('bill_date', [$start, $end])
            ->where('status', 'paid')
            ->sum('total_amount');

        // Bill counts
        $totalBills = MedicalBill::whereBetween('bill_date', [$start, $end])->count();
        $paidBills = MedicalBill::whereBetween('bill_date', [$start, $end])
            ->where('status', 'paid')
            ->count();
        $unpaidBills = $totalBills - $paidBills;

        // Payment methods breakdown
        $paymentMethods = MedicalBill::whereBetween('bill_date', [$start, $end])
            ->where('status', 'paid')
            ->select('payment_method', DB::raw('SUM(total_amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'payment_method' => $item->payment_method ?? 'cash',
                    'total_amount' => (float) $item->total_amount,
                    'count' => $item->count
                ];
            })
            ->toArray();

        // Daily revenue trend
        $dailyRevenue = MedicalBill::whereBetween('bill_date', [$start, $end])
            ->where('status', 'paid')
            ->select(
                DB::raw('DATE(bill_date) as date'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->groupBy(DB::raw('DATE(bill_date)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'amount' => (float) $item->amount
                ];
            })
            ->toArray();

        // Revenue trends (same as daily revenue but as key-value pairs for charts)
        $revenueTrends = collect($dailyRevenue)->pluck('amount', 'date')->toArray();

        // Services rendered from bill items
        $servicesRendered = DB::table('bill_items')
            ->join('medical_bills', 'bill_items.medical_bill_id', '=', 'medical_bills.id')
            ->whereBetween('medical_bills.bill_date', [$start, $end])
            ->where('medical_bills.status', 'paid')
            ->select(
                'bill_items.service_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(bill_items.total_price) as revenue')
            )
            ->groupBy('bill_items.service_name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'service_name' => $item->service_name,
                    'count' => $item->count,
                    'revenue' => (float) $item->revenue
                ];
            })
            ->toArray();

        return response()->json([
            'total_revenue' => (float) $totalRevenue,
            'total_transactions' => $totalBills,
            'total_bills' => $totalBills,
            'paid_bills' => $paidBills,
            'unpaid_bills' => $unpaidBills,
            'payment_methods' => $paymentMethods,
            'daily_revenue' => $dailyRevenue,
            'revenue_trends' => $revenueTrends,
            'services_rendered' => $servicesRendered,
        ]);
    }
}

