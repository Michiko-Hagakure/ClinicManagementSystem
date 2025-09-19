<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Transaction;
use App\Services\EmrApiService;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private EmrApiService $emrApiService;

    public function __construct(EmrApiService $emrApiService)
    {
        $this->emrApiService = $emrApiService;
    }

    /**
     * Display transaction history with search and filters.
     */
    public function index(Request $request): View
    {
        // Get transactions from file storage
        $transactionsFile = storage_path('app/transactions.json');
        $transactions = collect([]);
        
        if (file_exists($transactionsFile)) {
            $data = json_decode(file_get_contents($transactionsFile), true);
            $transactions = collect($data)->map(function($item) {
                $obj = (object) $item;
                $obj->created_at = \Carbon\Carbon::parse($item['created_at']);
                return $obj;
            })->sortByDesc('created_at');
        }

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show form for creating new transaction.
     */
    public function create(): View
    {
        // Mock service prices - will be fetched from database
        $services = [
            'Consultation' => 300.00,
            'X-ray' => 350.00,
            'ECG' => 250.00,
            'Ultrasound' => 1000.00,
            'Pre-natal Package' => 1500.00, // Bundled discount package
            'Pre-Employment/Annual Medical Exam' => 2200.00, // Comprehensive medical screening package
            'Laboratory' => 'expandable' // Special marker for expandable service
        ];

        // Detailed laboratory tests organized by category
        $laboratoryTests = [
            'Blood Tests' => [
                'Complete Blood Count (CBC)' => 180.00,
                'Metabolic Panels' => 220.00,
                'Lipid Panel' => 150.00,
                'Thyroid Function Tests' => 280.00,
                'Coagulation Panel' => 200.00,
                'Enzyme Tests' => 190.00,
            ],
            'Other Tests' => [
                'Urinalysis' => 120.00,
                'Microbiology Tests' => 160.00,
                'Genetic Tests' => 450.00,
                'Tumor Marker Tests' => 380.00,
            ]
        ];

        return view('transactions.create', compact('services', 'laboratoryTests'));
    }

    /**
     * Store new transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_id' => 'nullable|string|max:50',
            'services' => 'required|array|min:1',
            'services.*' => 'string',
            'payment_method' => 'required|in:cash,gcash,paymaya',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'chief_complaint' => 'required|string',
            'consultation_notes' => 'nullable|string',
            'assigned_doctor' => 'required|string|max:255',
            'appointment_time' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:50'
        ]);

        // Calculate totals
        $servicesList = [
            'Consultation' => 300.00,
            'X-ray' => 350.00,
            'ECG' => 250.00,
            'Ultrasound' => 1000.00,
            'Pre-natal Package' => 1500.00, // Bundled discount package
            'Pre-Employment/Annual Medical Exam' => 2200.00, // Comprehensive medical screening package
            'Laboratory' => 0.00, // Will be calculated from individual tests
            // Individual Laboratory Tests
            'Complete Blood Count (CBC)' => 180.00,
            'Metabolic Panels' => 220.00,
            'Lipid Panel' => 150.00,
            'Thyroid Function Tests' => 280.00,
            'Coagulation Panel' => 200.00,
            'Enzyme Tests' => 190.00,
            'Urinalysis' => 120.00,
            'Microbiology Tests' => 160.00,
            'Genetic Tests' => 450.00,
            'Tumor Marker Tests' => 380.00
        ];

        $selectedServices = [];
        $serviceTotal = 0;
        
        foreach ($validated['services'] as $serviceName) {
            if (isset($servicesList[$serviceName])) {
                $selectedServices[] = [
                    'name' => $serviceName,
                    'price' => $servicesList[$serviceName]
                ];
                $serviceTotal += $servicesList[$serviceName];
            }
        }

        // Get existing transactions for ID generation
        $transactionsFile = storage_path('app/transactions.json');
        $existingTransactions = [];
        if (file_exists($transactionsFile)) {
            $existingTransactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        }

        // Generate unique transaction ID and receipt number
        $transactionId = 'TXN-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);
        $receiptNumber = 'RCP-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);

        // Create transaction object
        $transaction = [
            'transaction_id' => $transactionId,
            'patient_name' => $validated['patient_name'],
            'patient_id' => $validated['patient_id'] ?? 'WALK-IN',
            'services' => $selectedServices,
            'service_total' => $serviceTotal,
            'medicine_total' => 0.00,
            'total_amount' => $serviceTotal,
            'payment_method' => ucfirst($validated['payment_method']),
            'amount_paid' => $validated['amount_paid'],
            'change_amount' => $validated['amount_paid'] - $serviceTotal,
            'status' => 'Paid',
            'cashier' => 'Cashier 1',
            'receipt_number' => $receiptNumber,
            'notes' => $validated['notes'],
            'chief_complaint' => $validated['chief_complaint'],
            'consultation_notes' => $validated['consultation_notes'],
            'assigned_doctor' => $validated['assigned_doctor'],
            'appointment_time' => $validated['appointment_time'] ?? 'Today, ' . date('g:i A'),
            'room_number' => $validated['room_number'],
            'created_at' => now()->toISOString()
        ];

        // Save to file
        $existingTransactions[] = $transaction;
        
        // Ensure storage directory exists
        $storageDir = dirname($transactionsFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        $result = file_put_contents($transactionsFile, json_encode($existingTransactions, JSON_PRETTY_PRINT));
        
        // Create consultation in EMR if patient has an ID
        if (!empty($validated['patient_id']) && $validated['patient_id'] !== 'WALK-IN') {
            $this->createEmrConsultation($validated);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', "Transaction {$transactionId} processed successfully! Receipt printed.");
    }

    /**
     * Display specific transaction details.
     */
    public function show(string $id): View
    {
        // Get transactions from file storage
        $transactionsFile = storage_path('app/transactions.json');
        $transaction = null;
        
        if (file_exists($transactionsFile)) {
            $data = json_decode(file_get_contents($transactionsFile), true);
            foreach ($data as $item) {
                if ($item['transaction_id'] === $id) {
                    $transaction = (object) $item;
                    $transaction->created_at = \Carbon\Carbon::parse($item['created_at']);
                    break;
                }
            }
        }
        
        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Display receipt for printing.
     */
    public function receipt(string $receiptNumber)
    {
        // Get transactions from file storage
        $transactionsFile = storage_path('app/transactions.json');
        $transaction = null;
        
        if (file_exists($transactionsFile)) {
            $data = json_decode(file_get_contents($transactionsFile), true);
            foreach ($data as $item) {
                if ($item['receipt_number'] === $receiptNumber) {
                    $transaction = $item; // Keep as array for the receipt template
                    break;
                }
            }
        }
        
        if (!$transaction) {
            // Create a sample transaction for testing if not found
            $transaction = [
                'receipt_number' => $receiptNumber,
                'transaction_id' => 'TXN-SAMPLE',
                'patient_name' => 'Sample Patient',
                'patient_id' => 'SAMPLE-001',
                'cashier' => 'Cashier 1',
                'services' => [
                    ['name' => 'Consultation', 'price' => 300.00],
                    ['name' => 'X-ray', 'price' => 350.00]
                ],
                'medicines' => [],
                'total_amount' => 650.00,
                'payment_method' => 'cash',
                'amount_paid' => 650.00,
                'change_amount' => 0.00,
                'assigned_doctor' => 'Dr. Maria Santos',
                'appointment_time' => 'Today, 2:30 PM',
                'room_number' => 'Room 102',
                'created_at' => now()->toISOString()
            ];
        }

        // Debug: Log the transaction data
        \Log::info('Receipt Transaction Data:', $transaction);
        
        // Return raw HTML response to avoid any layout inheritance
        $html = view('transactions.receipt', compact('transaction'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Create consultation in EMR system with billing data
     */
    private function createEmrConsultation(array $validatedData): void
    {
        try {
            Log::info('POS: Creating consultation in EMR', [
                'patient_id' => $validatedData['patient_id'],
                'chief_complaint' => $validatedData['chief_complaint']
            ]);

            $consultationData = [
                'patient_id' => $validatedData['patient_id'],
                'date' => now()->toDateString(),
                'chief_complaint' => $validatedData['chief_complaint'],
                'consultation_notes' => $validatedData['consultation_notes'] ?? '',
                'status' => 'pending',
                // Default vital signs - will be updated by doctor during examination
                'bp' => '0/0',
                'temparature' => 0,
                'weight' => 0,
                'o2' => 0,
                'pr' => 0
            ];

            $response = $this->emrApiService->createConsultation($consultationData);
            
            if ($response) {
                Log::info('POS: Successfully created consultation in EMR', ['consultation_id' => $response['id'] ?? 'unknown']);
            } else {
                Log::warning('POS: Failed to create consultation in EMR - no response');
            }
            
        } catch (\Exception $e) {
            Log::error('POS: Error creating consultation in EMR', [
                'error' => $e->getMessage(),
                'patient_id' => $validatedData['patient_id']
            ]);
        }
    }
}
