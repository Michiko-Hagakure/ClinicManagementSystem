<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\DispenseMedicine;
use App\Models\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Inventory reports dashboard for clinic owner
     */
    public function reports(Request $request)
    {
        // Date range filter (default: last 30 days)
        $fromDate = $request->get('from_date', now()->subDays(30)->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        // Overall statistics
        $stats = [
            'total_medicines' => Medicine::count(),
            'total_inventory_value' => Medicine::sum(DB::raw('stock_quantity * price')),
            'low_stock_count' => Medicine::lowStock()->count(),
            'out_of_stock_count' => Medicine::where('stock_quantity', 0)->count(),
            'total_dispensed_period' => DispenseMedicine::whereBetween('date', [$fromDate, $toDate])->sum('quantity'),
            'revenue_period' => $this->calculateRevenue($fromDate, $toDate),
            'active_alerts' => LowStockAlert::whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })->count()
        ];

        // Top dispensed medicines in period
        $topDispensed = DispenseMedicine::select('medicine_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('medicine')
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('medicine_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Low stock medicines
        $lowStockMedicines = Medicine::lowStock()->orderBy('stock_quantity')->limit(10)->get();

        // Recent dispensations
        $recentDispensations = DispenseMedicine::with('medicine')
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        // Stock status distribution for pie chart
        $totalMedicines = Medicine::count();
        $outOfStock = Medicine::where('stock_quantity', 0)->count();
        $lowStock = Medicine::lowStock()->where('stock_quantity', '>', 0)->count();
        $inStock = $totalMedicines - $outOfStock - $lowStock;
        
        $stockDistribution = [
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock
        ];

        // Daily dispensation trend for line chart
        $dailyDispensation = DispenseMedicine::select(
                DB::raw('DATE(date) as dispensation_date'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('dispensation_date')
            ->orderBy('dispensation_date')
            ->get();

        return view('inventory.reports', compact(
            'stats', 'topDispensed', 'lowStockMedicines', 'recentDispensations', 
            'stockDistribution', 'dailyDispensation', 'fromDate', 'toDate'
        ));
    }

    /**
     * Low stock report
     */
    public function lowStockReport()
    {
        $lowStockMedicines = Medicine::lowStock()
            ->orderBy('stock_quantity')
            ->paginate(20);

        $activeAlerts = LowStockAlert::with('medicine')
            ->whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })
            ->orderBy('alert_date', 'desc')
            ->paginate(20);

        return view('inventory.low-stock', compact('lowStockMedicines', 'activeAlerts'));
    }

    /**
     * Dispensation report
     */
    public function dispensationReport(Request $request)
    {
        $query = DispenseMedicine::with('medicine');

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Filter by patient if provided
        if ($request->has('patient_id') && $request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by medicine if provided
        if ($request->has('medicine_id') && $request->medicine_id) {
            $query->where('medicine_id', $request->medicine_id);
        }

        $dispensations = $query->orderBy('date', 'desc')->paginate(30);

        // Summary statistics for the filtered results
        $summary = [
            'total_dispensations' => $dispensations->total(),
            'total_quantity' => DispenseMedicine::when($request->from_date, function($q) use ($request) {
                    return $q->whereDate('date', '>=', $request->from_date);
                })
                ->when($request->to_date, function($q) use ($request) {
                    return $q->whereDate('date', '<=', $request->to_date);
                })
                ->when($request->patient_id, function($q) use ($request) {
                    return $q->where('patient_id', $request->patient_id);
                })
                ->when($request->medicine_id, function($q) use ($request) {
                    return $q->where('medicine_id', $request->medicine_id);
                })
                ->sum('quantity'),
            'estimated_revenue' => $this->calculateRevenue(
                $request->from_date ?? null, 
                $request->to_date ?? null,
                $request->patient_id ?? null,
                $request->medicine_id ?? null
            )
        ];

        return view('inventory.dispensation-report', compact('dispensations', 'summary'));
    }

    /**
     * Export data (CSV format)
     */
    public function exportData(Request $request, $type)
    {
        switch ($type) {
            case 'medicines':
                return $this->exportMedicines($request);
            case 'dispensations':
                return $this->exportDispensations($request);
            case 'low-stock':
                return $this->exportLowStock($request);
            case 'alerts':
                return $this->exportAlerts($request);
            default:
                return back()->with('error', 'Invalid export type');
        }
    }

    /**
     * API endpoint for low stock alerts (for other services)
     */
    public function apiLowStockAlerts()
    {
        $alerts = LowStockAlert::with('medicine')
            ->whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })
            ->orderBy('alert_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'alerts' => $alerts,
            'count' => $alerts->count()
        ]);
    }

    /**
     * Calculate revenue for a period
     */
    private function calculateRevenue($fromDate = null, $toDate = null, $patientId = null, $medicineId = null)
    {
        $query = DispenseMedicine::select(
            'dispense_medicine.quantity',
            'medicine.price'
        )
        ->join('medicine', 'dispense_medicine.medicine_id', '=', 'medicine.medicine_id');

        if ($fromDate) {
            $query->whereDate('dispense_medicine.date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('dispense_medicine.date', '<=', $toDate);
        }
        if ($patientId) {
            $query->where('dispense_medicine.patient_id', $patientId);
        }
        if ($medicineId) {
            $query->where('dispense_medicine.medicine_id', $medicineId);
        }

        return $query->get()->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Export medicines inventory
     */
    private function exportMedicines(Request $request)
    {
        $medicines = Medicine::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="medicines_inventory_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($medicines) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'Name', 'Dosage', 'Stock Quantity', 'Price', 'Stock Value', 'Status']);
            
            foreach ($medicines as $medicine) {
                $status = $medicine->stock_quantity == 0 ? 'Out of Stock' : 
                         ($medicine->isLowStock() ? 'Low Stock' : 'In Stock');
                         
                fputcsv($file, [
                    $medicine->medicine_id,
                    $medicine->name,
                    $medicine->dosage,
                    $medicine->stock_quantity,
                    $medicine->price,
                    $medicine->stock_quantity * $medicine->price,
                    $status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export dispensations
     */
    private function exportDispensations(Request $request)
    {
        $query = DispenseMedicine::with('medicine');
        
        // Apply filters if provided
        if ($request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        
        $dispensations = $query->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dispensations_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($dispensations) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Date', 'Patient ID', 'Medicine', 'Dosage', 'Quantity', 'Unit Price', 'Total Cost']);
            
            foreach ($dispensations as $dispensation) {
                fputcsv($file, [
                    $dispensation->date->format('Y-m-d H:i:s'),
                    $dispensation->patient_id,
                    $dispensation->medicine->name ?? 'N/A',
                    $dispensation->medicine->dosage ?? 'N/A',
                    $dispensation->quantity,
                    $dispensation->medicine->price ?? 0,
                    $dispensation->quantity * ($dispensation->medicine->price ?? 0)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export low stock medicines
     */
    private function exportLowStock(Request $request)
    {
        $medicines = Medicine::lowStock()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="low_stock_medicines_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($medicines) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['ID', 'Name', 'Dosage', 'Current Stock', 'Price', 'Status']);
            
            foreach ($medicines as $medicine) {
                $status = $medicine->stock_quantity == 0 ? 'Out of Stock' : 'Low Stock';
                         
                fputcsv($file, [
                    $medicine->medicine_id,
                    $medicine->name,
                    $medicine->dosage,
                    $medicine->stock_quantity,
                    $medicine->price,
                    $status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export alerts
     */
    private function exportAlerts(Request $request)
    {
        $alerts = LowStockAlert::with('medicine')
            ->whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })
            ->orderBy('alert_date', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_alerts_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($alerts) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Alert Date', 'Medicine', 'Dosage', 'Current Stock', 'Threshold', 'Days Since Alert']);
            
            foreach ($alerts as $alert) {
                fputcsv($file, [
                    $alert->alert_date->format('Y-m-d H:i:s'),
                    $alert->medicine->name ?? 'N/A',
                    $alert->medicine->dosage ?? 'N/A',
                    $alert->medicine->stock_quantity ?? 0,
                    $alert->threshold,
                    $alert->alert_date->diffInDays(now())
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
