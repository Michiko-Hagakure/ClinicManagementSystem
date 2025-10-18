<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    /**
     * Display medicine inventory list
     */
    public function index(Request $request)
    {
        $query = Medicine::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->where('stock_quantity', 0);
                    break;
                case 'available':
                    $query->where('stock_quantity', '>', 10);
                    break;
            }
        }
        
        $medicines = $query->orderBy('category')->orderBy('name')->paginate(20);
        
        // Get all categories for filter dropdown
        $categories = [
            'Capsules/Tablets',
            'Antibiotics (TABS/CAPS)',
            'Suspension',
            'Drops/Ointment/Cream/Nebule',
            'IV Meds (VIAL/AMPULE)'
        ];
        
        return view('medicine.index', compact('medicines', 'categories'));
    }

    /**
     * Show the form for creating a new medicine
     */
    public function create()
    {
        return view('medicine.create');
    }

    /**
     * Store a newly created medicine in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:25',
            'category' => 'required|string|in:Capsules/Tablets,Antibiotics (TABS/CAPS),Suspension,Drops/Ointment/Cream/Nebule,IV Meds (VIAL/AMPULE)',
            'stock_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        try {
            $medicine = Medicine::create($validated);
            
            return redirect()->route('medicine.index')
                ->with('success', 'Medicine "' . $medicine->name . '" added successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to add medicine: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified medicine
     */
    public function show(Medicine $medicine)
    {
        $medicine->load(['dispensedMedicines.medicine', 'lowStockAlerts']);
        
        // Get recent dispensations for this medicine
        $recentDispensations = $medicine->dispensedMedicines()
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
            
        return view('medicine.show', compact('medicine', 'recentDispensations'));
    }

    /**
     * Show the form for editing the specified medicine
     */
    public function edit(Medicine $medicine)
    {
        return view('medicine.edit', compact('medicine'));
    }

    /**
     * Update the specified medicine in storage
     */
    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:25',
            'category' => 'required|string|in:Capsules/Tablets,Antibiotics (TABS/CAPS),Suspension,Drops/Ointment/Cream/Nebule,IV Meds (VIAL/AMPULE)',
            'price' => 'required|numeric|min:0'
        ]);

        try {
            $medicine->update($validated);
            
            return redirect()->route('medicine.show', $medicine)
                ->with('success', 'Medicine updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update medicine: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified medicine from storage
     */
    public function destroy(Medicine $medicine)
    {
        try {
            // Check if medicine has been dispensed
            if ($medicine->dispensedMedicines()->count() > 0) {
                return back()->with('error', 'Cannot delete medicine that has dispensation history.');
            }
            
            $medicineName = $medicine->name;
            $medicine->delete();
            
            return redirect()->route('medicine.index')
                ->with('success', "Medicine \"{$medicineName}\" deleted successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete medicine: ' . $e->getMessage());
        }
    }

    /**
     * Update stock quantity
     */
    public function updateStock(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'action' => 'required|in:add,set',
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $oldStock = $medicine->stock_quantity;
            
            if ($validated['action'] === 'add') {
                $medicine->addStock($validated['quantity']);
                $message = "Added {$validated['quantity']} units. Stock: {$oldStock} → {$medicine->stock_quantity}";
            } else {
                $medicine->stock_quantity = $validated['quantity'];
                $medicine->save();
                $message = "Set stock to {$validated['quantity']} units. Previous: {$oldStock}";
            }

            // Remove low stock alert if stock is now sufficient
            if ($medicine->stock_quantity > 10) {
                LowStockAlert::removeAlert($medicine->medicine_id);
            } elseif ($medicine->isLowStock()) {
                // Create low stock alert if needed
                LowStockAlert::createAlert($medicine->medicine_id);
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update stock: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for medicine search (for other services)
     */
    public function apiSearch(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 10);
        
        $medicines = Medicine::when($search, function($query, $search) {
                return $query->search($search);
            })
            ->where('stock_quantity', '>', 0) // Only return medicines in stock
            ->orderBy('name')
            ->limit($limit)
            ->get();
            
        return response()->json($medicines);
    }

    /**
     * API endpoint to get medicine details
     */
    public function apiShow($medicineId)
    {
        $medicine = Medicine::where('medicine_id', $medicineId)->first();
        
        if ($medicine) {
            return response()->json([
                'success' => true,
                'medicine' => $medicine
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
    }

    /**
     * API endpoint to reduce stock (for POS integration)
     */
    public function apiReduceStock(Request $request, $medicineId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'patient_id' => 'nullable|integer'
        ]);

        try {
            DB::beginTransaction();

            $medicine = Medicine::where('medicine_id', $medicineId)->first();
            
            if (!$medicine) {
                return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
            }

            if ($medicine->stock_quantity < $validated['quantity']) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Insufficient stock',
                    'available' => $medicine->stock_quantity
                ], 400);
            }

            // Reduce stock
            $medicine->reduceStock($validated['quantity']);

            // Log the dispensation
            \App\Models\DispenseMedicine::create([
                'medicine_id' => $medicine->medicine_id,
                'patient_id' => $validated['patient_id'] ?? null,
                'quantity' => $validated['quantity'],
                'date' => now()
            ]);

            // Create low stock alert if needed
            if ($medicine->isLowStock()) {
                LowStockAlert::createAlert($medicine->medicine_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock reduced successfully',
                'remaining_stock' => $medicine->stock_quantity
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
