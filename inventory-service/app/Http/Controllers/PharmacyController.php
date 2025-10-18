<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\DispenseMedicine;
use App\Models\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Pharmacy Staff Dashboard
     */
    public function dashboard()
    {
        // Get medicine inventory with stock levels
        $medicines = Medicine::orderBy('name')->paginate(15);
        
        // Get low stock alerts
        $lowStockAlerts = LowStockAlert::with('medicine')
            ->whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })
            ->orderBy('alert_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent dispensations
        $recentDispensations = DispenseMedicine::with('medicine')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Calculate statistics
        $stats = [
            'total_medicines' => Medicine::count(),
            'low_stock_count' => Medicine::lowStock()->count(),
            'out_of_stock_count' => Medicine::where('stock_quantity', 0)->count(),
            'total_dispensed_today' => DispenseMedicine::whereDate('date', today())->sum('quantity')
        ];

        return view('pharmacy.dashboard', compact('medicines', 'lowStockAlerts', 'recentDispensations', 'stats'));
    }

    /**
     * Search medicines
     */
    public function searchMedicines(Request $request)
    {
        $search = $request->get('q', '');
        
        $medicines = Medicine::when($search, function($query, $search) {
            return $query->search($search);
        })->orderBy('name')->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'medicines' => $medicines->items(),
                'pagination' => [
                    'current_page' => $medicines->currentPage(),
                    'last_page' => $medicines->lastPage(),
                    'total' => $medicines->total()
                ]
            ]);
        }

        return view('pharmacy.search', compact('medicines', 'search'));
    }

    /**
     * Show dispensing form
     */
    public function showDispenseForm()
    {
        $medicines = Medicine::where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();
            
        return view('pharmacy.dispense', compact('medicines'));
    }

    /**
     * Dispense medicine
     */
    public function dispenseMedicine(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'medicine_id' => 'required|exists:medicine,medicine_id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Get medicine
            $medicine = Medicine::where('medicine_id', $validated['medicine_id'])->first();
            
            if (!$medicine) {
                return back()->with('error', 'Medicine not found.');
            }

            // Check stock availability
            if ($medicine->stock_quantity < $validated['quantity']) {
                return back()->with('error', 'Insufficient stock. Available: ' . $medicine->stock_quantity);
            }

            // Create dispensation record
            $dispensation = DispenseMedicine::create([
                'patient_id' => $validated['patient_id'],
                'medicine_id' => $validated['medicine_id'],
                'quantity' => $validated['quantity'],
                'date' => now()
            ]);

            // Reduce stock
            $medicine->reduceStock($validated['quantity']);

            // Check if medicine is now low stock and create alert
            if ($medicine->isLowStock()) {
                LowStockAlert::createAlert($medicine->medicine_id);
            }

            DB::commit();

            return redirect()->back()->with('success', 
                "Successfully dispensed {$validated['quantity']} units of {$medicine->name} to patient #{$validated['patient_id']}. Remaining stock: {$medicine->stock_quantity}"
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to dispense medicine: ' . $e->getMessage());
        }
    }

    /**
     * View low stock alerts
     */
    public function lowStockAlerts()
    {
        $alerts = LowStockAlert::with('medicine')
            ->whereHas('medicine', function($query) {
                $query->whereRaw('stock_quantity <= low_stock_alerts.threshold');
            })
            ->orderBy('alert_date', 'desc')
            ->paginate(20);

        return view('pharmacy.alerts', compact('alerts'));
    }

    /**
     * Dismiss low stock alert (when stock is replenished)
     */
    public function dismissAlert(Request $request, $alertId)
    {
        $alert = LowStockAlert::find($alertId);
        
        if ($alert) {
            $medicine = $alert->medicine;
            
            // Check if stock has been replenished
            if ($medicine && $medicine->stock_quantity > $alert->threshold) {
                $alert->delete();
                return back()->with('success', 'Alert dismissed successfully.');
            } else {
                return back()->with('error', 'Cannot dismiss alert. Stock is still low.');
            }
        }
        
        return back()->with('error', 'Alert not found.');
    }

    /**
     * Get medicine details for AJAX
     */
    public function getMedicineDetails($medicineId)
    {
        $medicine = Medicine::where('medicine_id', $medicineId)->first();
        
        if ($medicine) {
            return response()->json([
                'success' => true,
                'medicine' => [
                    'medicine_id' => $medicine->medicine_id,
                    'name' => $medicine->name,
                    'dosage' => $medicine->dosage,
                    'stock_quantity' => $medicine->stock_quantity,
                    'price' => $medicine->price,
                    'formatted_price' => $medicine->formatted_price
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Medicine not found']);
    }

    /**
     * View dispensation history
     */
    public function dispensationHistory(Request $request)
    {
        $query = DispenseMedicine::with('medicine');

        // Filter by patient ID if provided
        if ($request->has('patient_id') && $request->patient_id) {
            $query->where('patient_id', $request->patient_id);
        }

        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $dispensations = $query->orderBy('date', 'desc')->paginate(20);

        return view('pharmacy.history', compact('dispensations'));
    }
}
