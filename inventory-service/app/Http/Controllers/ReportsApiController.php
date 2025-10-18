<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\DispenseMedicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsApiController extends Controller
{
    /**
     * Get inventory statistics for owner dashboard
     */
    public function getInventoryReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfDay());

        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Total items in inventory
        $totalItems = Medicine::sum('stock_quantity');

        // Low stock items (quantity < 50)
        $lowStockItems = Medicine::where('stock_quantity', '<', 50)->count();

        // Expiring soon items - since there's no expiry_date column, default to 0
        $expiringSoon = 0;

        // Total stock value
        $stockValue = Medicine::selectRaw('SUM(stock_quantity * price) as total_value')
            ->value('total_value') ?? 0;

        // Top selling medicines (based on dispense records in the period)
        $topSelling = DispenseMedicine::whereBetween('date', [$start, $end])
            ->join('medicine', 'dispense_medicine.medicine_id', '=', 'medicine.medicine_id')
            ->select(
                'medicine.medicine_id as id',
                'medicine.name',
                'medicine.dosage as description',
                'medicine.stock_quantity',
                DB::raw('SUM(dispense_medicine.quantity) as total_quantity_sold')
            )
            ->groupBy('medicine.medicine_id', 'medicine.name', 'medicine.dosage', 'medicine.stock_quantity')
            ->orderByDesc('total_quantity_sold')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'medicine' => [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'stock_quantity' => $item->stock_quantity,
                        'reorder_level' => 50, // Default reorder level
                    ],
                    'total_quantity_sold' => (int) $item->total_quantity_sold
                ];
            })
            ->toArray();

        return response()->json([
            'total_medicines' => Medicine::count(),
            'total_items' => (int) $totalItems,
            'total_stock_value' => (float) $stockValue,
            'low_stock_items' => $lowStockItems,
            'expiring_items' => $expiringSoon,
            'expiring_soon' => $expiringSoon,
            'stock_value' => (float) $stockValue,
            'top_selling' => $topSelling,
            'top_selling_medicines' => $topSelling,
        ]);
    }
}

