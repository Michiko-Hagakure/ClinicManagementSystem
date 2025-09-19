<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PharmacyController extends Controller
{
    /**
     * Display pharmacy sales interface.
     */
    public function sales(): View
    {
        // Mock medicine data - will be replaced with inventory service API calls
        $medicines = collect([
            (object) [
                'id' => 'MED-001',
                'name' => 'Paracetamol',
                'dosage' => '500mg',
                'price' => 5.00,
                'stock' => 150,
                'category' => 'Pain Relief'
            ],
            (object) [
                'id' => 'MED-002', 
                'name' => 'Amoxicillin',
                'dosage' => '250mg',
                'price' => 2.50,
                'stock' => 80,
                'category' => 'Antibiotic'
            ],
            (object) [
                'id' => 'MED-003',
                'name' => 'Ibuprofen',
                'dosage' => '400mg', 
                'price' => 8.00,
                'stock' => 45,
                'category' => 'Anti-inflammatory'
            ],
            (object) [
                'id' => 'MED-004',
                'name' => 'Cetirizine',
                'dosage' => '10mg',
                'price' => 3.50,
                'stock' => 25,
                'category' => 'Antihistamine'
            ]
        ]);

        return view('pharmacy.sales', compact('medicines'));
    }

    /**
     * Search medicines interface.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        
        // Mock search results - will be replaced with inventory service API
        $medicines = collect([
            (object) [
                'id' => 'MED-001',
                'name' => 'Paracetamol',
                'dosage' => '500mg',
                'price' => 5.00,
                'stock' => 150,
                'category' => 'Pain Relief',
                'description' => 'For fever and pain relief'
            ],
            (object) [
                'id' => 'MED-002',
                'name' => 'Amoxicillin', 
                'dosage' => '250mg',
                'price' => 2.50,
                'stock' => 80,
                'category' => 'Antibiotic',
                'description' => 'Broad-spectrum antibiotic'
            ]
        ]);

        if ($query) {
            $medicines = $medicines->filter(function($medicine) use ($query) {
                return stripos($medicine->name, $query) !== false ||
                       stripos($medicine->category, $query) !== false;
            });
        }

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
            'medicines.*.id' => 'required|string',
            'medicines.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,gcash,paymaya'
        ]);

        // Get medicine data for price calculation
        $medicineDatabase = collect([
            'MED-001' => ['name' => 'Paracetamol', 'dosage' => '500mg', 'price' => 5.00],
            'MED-002' => ['name' => 'Amoxicillin', 'dosage' => '250mg', 'price' => 2.50],
            'MED-003' => ['name' => 'Ibuprofen', 'dosage' => '400mg', 'price' => 8.00],
            'MED-004' => ['name' => 'Cetirizine', 'dosage' => '10mg', 'price' => 3.50]
        ]);

        // Calculate medicine total and build medicine list
        $medicineTotal = 0;
        $medicinesList = [];
        
        foreach ($validated['medicines'] as $medicineData) {
            $medicineId = $medicineData['id'];
            $quantity = $medicineData['quantity'];
            
            if (isset($medicineDatabase[$medicineId])) {
                $medicine = $medicineDatabase[$medicineId];
                $subtotal = $medicine['price'] * $quantity;
                $medicineTotal += $subtotal;
                
                $medicinesList[] = [
                    'name' => $medicine['name'] . ' ' . $medicine['dosage'],
                    'quantity' => $quantity,
                    'price' => $medicine['price'],
                    'subtotal' => $subtotal
                ];
            }
        }

        // Get existing transactions
        $transactionsFile = storage_path('app/transactions.json');
        $existingTransactions = [];
        if (file_exists($transactionsFile)) {
            $existingTransactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        }

        // Look for existing transaction for same patient today
        $today = now()->format('Y-m-d');
        $patientId = $validated['patient_id'] ?? 'WALK-IN';
        $patientName = $validated['patient_name'];
        $existingTransactionIndex = null;
        
        foreach ($existingTransactions as $index => $transaction) {
            $transactionDate = \Carbon\Carbon::parse($transaction['created_at'])->format('Y-m-d');
            
            // Only match if it's the same day
            if ($transactionDate !== $today) {
                continue;
            }
            
            // Priority 1: Exact patient ID match (but not WALK-IN)
            if ($patientId !== 'WALK-IN' && isset($transaction['patient_id']) && 
                $transaction['patient_id'] === $patientId) {
                $existingTransactionIndex = $index;
                break;
            }
            
            // Priority 2: If no patient ID, match by exact name AND no existing patient ID
            if ($patientId === 'WALK-IN' && 
                (!isset($transaction['patient_id']) || $transaction['patient_id'] === 'WALK-IN') &&
                $transaction['patient_name'] === $patientName) {
                $existingTransactionIndex = $index;
                break;
            }
        }

        if ($existingTransactionIndex !== null) {
            // Add medicines to existing transaction
            $existingTransaction = &$existingTransactions[$existingTransactionIndex];
            
            // Add medicines to existing transaction
            if (!isset($existingTransaction['medicines'])) {
                $existingTransaction['medicines'] = [];
            }
            $existingTransaction['medicines'] = array_merge($existingTransaction['medicines'], $medicinesList);
            
            // Update totals
            $existingTransaction['medicine_total'] = ($existingTransaction['medicine_total'] ?? 0) + $medicineTotal;
            $existingTransaction['total_amount'] = $existingTransaction['service_total'] + $existingTransaction['medicine_total'];
            
            // Update payment info (assuming additional payment for medicines)
            $existingTransaction['amount_paid'] = $existingTransaction['total_amount']; // Assume exact payment
            $existingTransaction['change_amount'] = 0.00;
            
            // Update notes
            $existingTransaction['notes'] = ($existingTransaction['notes'] ?? '') . ' + Medicine purchase';
            
            $transactionId = $existingTransaction['transaction_id'];
            $isNewTransaction = false;
        } else {
            // Create new transaction for medicine sale
            $transactionId = 'TXN-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);
            $receiptNumber = 'RCP-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);

            $transaction = [
                'transaction_id' => $transactionId,
                'patient_name' => $validated['patient_name'],
                'patient_id' => $patientId,
                'services' => [['name' => 'Medicine Purchase', 'price' => 0.00]], // Medicine-only transaction
                'medicines' => $medicinesList,
                'service_total' => 0.00,
                'medicine_total' => $medicineTotal,
                'total_amount' => $medicineTotal,
                'payment_method' => ucfirst($validated['payment_method']),
                'amount_paid' => $medicineTotal,
                'change_amount' => 0.00,
                'status' => 'Paid',
                'cashier' => 'Pharmacy Staff',
                'receipt_number' => $receiptNumber,
                'notes' => 'Medicine sale transaction',
                'transaction_type' => 'medicine_sale',
                'created_at' => now()->toISOString()
            ];

            $existingTransactions[] = $transaction;
            $isNewTransaction = true;
        }
        
        // Ensure storage directory exists
        $storageDir = dirname($transactionsFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        file_put_contents($transactionsFile, json_encode($existingTransactions, JSON_PRETTY_PRINT));
        
        // Return appropriate message based on whether transaction was updated or created
        if (isset($isNewTransaction) && $isNewTransaction) {
            $message = "Medicine sale transaction {$transactionId} created successfully!";
        } else {
            $message = "Medicines added to existing transaction {$transactionId} successfully! (Patient ID: {$patientId})";
        }
        
        return redirect()->route('transactions.index')
            ->with('success', $message);
    }

    /**
     * API endpoint for medicine search (AJAX).
     */
    public function apiSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        // Mock API response - will call inventory service
        $medicines = collect([
            [
                'id' => 'MED-001',
                'name' => 'Paracetamol',
                'dosage' => '500mg',
                'price' => 5.00,
                'stock' => 150,
                'category' => 'Pain Relief'
            ],
            [
                'id' => 'MED-002',
                'name' => 'Amoxicillin',
                'dosage' => '250mg', 
                'price' => 2.50,
                'stock' => 80,
                'category' => 'Antibiotic'
            ]
        ]);

        if ($query) {
            $medicines = $medicines->filter(function($medicine) use ($query) {
                return stripos($medicine['name'], $query) !== false;
            })->values();
        }

        return response()->json($medicines->take(10));
    }
}
