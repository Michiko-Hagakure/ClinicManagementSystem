<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Services\InventoryApiService;
use App\Services\EmrApiService;
use App\Models\MedicalBill;
use App\Models\BillItem;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PharmacyController extends Controller
{
    protected InventoryApiService $inventoryService;
    protected EmrApiService $emrApiService;

    public function __construct(InventoryApiService $inventoryService, EmrApiService $emrApiService)
    {
        $this->inventoryService = $inventoryService;
        $this->emrApiService = $emrApiService;
    }

    /**
     * Display pharmacy sales interface.
     */
    public function sales(): View
    {
        // Get medicines from inventory service
        $medicinesArray = $this->inventoryService->getAllMedicines(100);
        $medicines = collect($medicinesArray)->map(fn($medicine) => (object) $medicine);

        return view('pharmacy.sales', compact('medicines'));
    }

    /**
     * Search medicines interface.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        
        // Get medicines from inventory service
        $medicinesArray = $this->inventoryService->searchMedicines($query, 50);
        $medicines = collect($medicinesArray)->map(fn($medicine) => (object) $medicine);

        return view('pharmacy.search', compact('medicines', 'query'));
    }

    /**
     * Display pharmacy transaction history.
     */
    public function history(): View
    {
        // Mock pharmacy transaction history
        $transactions = collect([
            (object) [
                'id' => 'PS-001',
                'date' => now()->subHours(2),
                'patient_name' => 'Maria Santos',
                'medicines' => [
                    ['name' => 'Paracetamol 500mg', 'qty' => 10, 'price' => 50.00],
                    ['name' => 'Amoxicillin 250mg', 'qty' => 20, 'price' => 50.00]
                ],
                'total_amount' => 100.00,
                'cashier' => 'Cashier 1'
            ],
            (object) [
                'id' => 'PS-002',
                'date' => now()->subHours(4),
                'patient_name' => 'Juan Dela Cruz',
                'medicines' => [
                    ['name' => 'Ibuprofen 400mg', 'qty' => 15, 'price' => 120.00]
                ],
                'total_amount' => 120.00,
                'cashier' => 'Cashier 1'
            ]
        ]);

        return view('pharmacy.history', compact('transactions'));
    }

    /**
     * Process medicine sale.
     */
    public function sell(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_id' => 'nullable|string|max:50',
            'medicines' => 'required|array|min:1',
            'medicines.*.id' => 'required|integer',
            'medicines.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,gcash,paymaya'
        ]);

        $paymentMethodMap = [
            'cash' => 'cash',
            'gcash' => 'ewallet',
            'paymaya' => 'ewallet'
        ];

        try {
            DB::beginTransaction();

        // Calculate medicine total and build medicine list
        $medicineTotal = 0;
        $medicinesList = [];
            $medicineItems = []; // For database storage
            $stockReductions = []; // Track successful stock reductions for rollback if needed
        
        foreach ($validated['medicines'] as $medicineData) {
            $medicineId = $medicineData['id'];
            $quantity = $medicineData['quantity'];
            
                // Get medicine details from inventory service
                $medicine = $this->inventoryService->getMedicine($medicineId);
                
                if ($medicine) {
                    // Check stock availability
                    if ($medicine['stock'] < $quantity) {
                        DB::rollBack();
                        return back()->with('error', 
                            "Insufficient stock for {$medicine['name']}. Available: {$medicine['stock']}, Requested: {$quantity}");
                    }
                    
                    // Reduce stock in inventory service
                    $stockReduced = $this->inventoryService->reduceStock($medicineId, $quantity);
                    
                    if (!$stockReduced) {
                        DB::rollBack();
                        return back()->with('error', 
                            "Failed to reduce stock for {$medicine['name']}. Please try again.");
                    }
                    
                    $stockReductions[] = ['medicine_id' => $medicineId, 'quantity' => $quantity];
                    
                $subtotal = $medicine['price'] * $quantity;
                $medicineTotal += $subtotal;
                
                    // For JSON (backward compatibility)
                $medicinesList[] = [
                    'name' => $medicine['name'] . ' ' . $medicine['dosage'],
                    'quantity' => $quantity,
                    'price' => $medicine['price'],
                    'subtotal' => $subtotal
                ];
                    
                    // For database storage
                    $medicineItems[] = [
                        'medicine_id' => $medicineId,
                        'service_name' => $medicine['name'] . ' ' . $medicine['dosage'],
                        'service_category' => 'Medicine',
                        'quantity' => $quantity,
                        'unit_price' => $medicine['price'],
                        'total_price' => $subtotal,
                    ];
                } else {
                    DB::rollBack();
                    return back()->with('error', "Medicine with ID {$medicineId} not found in inventory.");
                }
            }

            // Get or create patient in POS database
            $localPatientId = 1; // Default for walk-ins
            $patientIdInput = $validated['patient_id'] ?? 'WALK-IN';
            
            if (!empty($patientIdInput) && $patientIdInput !== 'WALK-IN') {
                $localPatient = Patient::find($patientIdInput);
                
                if (!$localPatient) {
                    try {
                        $emrPatient = $this->emrApiService->getPatient($patientIdInput);
                        
                        if ($emrPatient) {
                            $localPatient = new Patient([
                                'patient_code' => $emrPatient['patient_code'] ?? 'P' . str_pad($patientIdInput, 4, '0', STR_PAD_LEFT),
                                'first_name' => $emrPatient['first_name'] ?? '',
                                'last_name' => $emrPatient['last_name'] ?? '',
                                'middle_name' => $emrPatient['middle_name'] ?? '',
                                'date_of_birth' => $emrPatient['date_of_birth'] ?? now()->subYears(30)->format('Y-m-d'),
                                'gender' => strtolower($emrPatient['gender'] ?? 'other'),
                                'phone' => $emrPatient['phone'] ?? '',
                                'email' => $emrPatient['email'] ?? null,
                                'address' => $emrPatient['address'] ?? '',
                                'civil_status' => $emrPatient['civil_status'] ?? null,
                                'insurance_provider' => null,
                            ]);
                            $localPatient->id = $patientIdInput;
                            $localPatient->save();
                            
                            $localPatientId = $localPatient->id;
                            Log::info('Created patient in POS database from EMR for medicine sale', ['patient_id' => $localPatientId]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to sync patient from EMR to POS for medicine sale', [
                            'patient_id' => $patientIdInput,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    $localPatientId = $localPatient->id;
                }
            }

            // Check if there's an existing bill for this patient today
            $today = now()->startOfDay();
            $existingBill = MedicalBill::where('patient_id', $localPatientId)
                ->whereDate('bill_date', $today)
                ->first();

            $paymentMethod = $paymentMethodMap[$validated['payment_method']] ?? 'cash';

            if ($existingBill) {
                // Update existing bill
                $existingBill->subtotal += $medicineTotal;
                $existingBill->total_amount += $medicineTotal;
                $existingBill->notes = ($existingBill->notes ?? '') . ' + Medicine purchase (₱' . number_format($medicineTotal, 2) . ')';
                $existingBill->bill_date = now(); // Update timestamp to current time
                $existingBill->save();

                // Add medicine items to bill
                foreach ($medicineItems as $item) {
                    BillItem::create([
                        'medical_bill_id' => $existingBill->id,
                        'medical_service_id' => null,
                        'medicine_id' => $item['medicine_id'] ?? null,
                        'service_name' => $item['service_name'],
                        'service_category' => $item['service_category'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'notes' => 'Medicine purchase',
                        'performed_by' => 'Pharmacy Staff',
                        'service_date' => now(),
                    ]);
                }

                $billNumber = $existingBill->bill_number;
                $message = "Medicines added to existing bill {$billNumber} successfully! (Patient ID: {$localPatientId})";
            } else {
                // Create new bill
                $billNumber = MedicalBill::generateBillNumber();
                
                $bill = MedicalBill::create([
                    'bill_number' => $billNumber,
                    'patient_id' => $localPatientId,
                    'subtotal' => $medicineTotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $medicineTotal,
                    'status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'notes' => 'Medicine purchase',
                    'cashier_name' => 'Pharmacy Staff',
                    'bill_date' => now(),
                    'paid_at' => now(),
                ]);

                // Add medicine items to bill
                foreach ($medicineItems as $item) {
                    BillItem::create([
                        'medical_bill_id' => $bill->id,
                        'medical_service_id' => null,
                        'medicine_id' => $item['medicine_id'] ?? null,
                        'service_name' => $item['service_name'],
                        'service_category' => $item['service_category'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'notes' => 'Medicine purchase',
                        'performed_by' => 'Pharmacy Staff',
                        'service_date' => now(),
                    ]);
                }

                $message = "Medicine sale bill {$billNumber} created successfully!";
            }

            // Also save to JSON for backward compatibility (receipts, etc.)
        $transactionsFile = storage_path('app/transactions.json');
        $existingTransactions = [];
        if (file_exists($transactionsFile)) {
            $existingTransactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        }

            $transactionId = 'TXN-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);

            $transaction = [
                'transaction_id' => $transactionId,
                'patient_name' => $validated['patient_name'],
                'patient_id' => $patientIdInput,
                'services' => [['name' => 'Medicine Purchase', 'price' => 0.00]],
                'medicines' => $medicinesList,
                'service_total' => 0.00,
                'medicine_total' => $medicineTotal,
                'total_amount' => $medicineTotal,
                'payment_method' => ucfirst($validated['payment_method']),
                'amount_paid' => $medicineTotal,
                'change_amount' => 0.00,
                'status' => 'Paid',
                'cashier' => 'Pharmacy Staff',
                'receipt_number' => $billNumber,
                'notes' => 'Medicine sale transaction',
                'transaction_type' => 'medicine_sale',
                'created_at' => now()->toISOString()
            ];

            $existingTransactions[] = $transaction;
        
        $storageDir = dirname($transactionsFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        file_put_contents($transactionsFile, json_encode($existingTransactions, JSON_PRETTY_PRINT));
        
            DB::commit();
        
        return redirect()->route('transactions.index')
            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Medicine sale error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to process medicine sale: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for medicine search (AJAX).
     */
    public function apiSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        // Get medicines from inventory service
        $medicines = $this->inventoryService->searchMedicines($query, 10);

        return response()->json($medicines);
    }
}
