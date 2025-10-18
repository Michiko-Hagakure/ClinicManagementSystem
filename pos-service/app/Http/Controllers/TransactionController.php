<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Transaction;
use App\Models\MedicalBill;
use App\Models\BillItem;
use Illuminate\Support\Facades\Log;
use App\Services\EmrApiService;

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
        // Start building the query
        $query = MedicalBill::with(['billItems', 'payments'])
            ->whereIn('status', ['paid', 'pending']);

        // Note: Search filtering is handled post-query since we display EMR patient names
        // but medical bills are linked to local patients. We'll filter the results after
        // mapping EMR patient data in the transformation step below.

        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('bill_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('bill_date', '<=', $request->date_to);
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $paymentMethodMap = [
                'Cash' => 'cash',
                'Card' => ['credit_card', 'debit_card'],
                'Insurance' => 'insurance'
            ];
            
            $selectedMethod = $paymentMethodMap[$request->payment_method] ?? $request->payment_method;
            
            if (is_array($selectedMethod)) {
                $query->whereIn('payment_method', $selectedMethod);
            } else {
                $query->where('payment_method', $selectedMethod);
            }
        }

        // Order by most recent first and paginate
        $bills = $query->orderBy('bill_date', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        // Transform bills to transaction format with EMR patient data
        $transactions = $bills->getCollection()->map(function($bill, $index) {
            // Get the actual patient data from the bill
            $patientName = 'Unknown Patient';
            $patientId = 'N/A';
            
            // First, try to get patient from local POS database
            if ($bill->patient) {
                $patientName = $bill->patient->full_name;
                $patientId = $bill->patient->id;
            } else {
                // If not found locally, try to fetch from EMR
                try {
                    if ($bill->patient_id) {
                        $emrPatient = $this->emrApiService->getPatient($bill->patient_id);
                        if ($emrPatient) {
                    $patientName = $emrPatient['full_name'] ?? $emrPatient['first_name'] . ' ' . $emrPatient['last_name'];
                    $patientId = $emrPatient['id'];
                        }
                }
            } catch (\Exception $e) {
                    Log::warning('Failed to fetch patient from EMR', [
                        'patient_id' => $bill->patient_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Build services array
            $services = [];
            $mostRecentActivityTime = $bill->bill_date ?? $bill->created_at;
            
            if ($bill->billItems && $bill->billItems->count() > 0) {
                $services = $bill->billItems->map(function($item) {
                    return [
                        'name' => $item->service_name,
                        'category' => $item->service_category,
                        'quantity' => $item->quantity ?? 1,
                        'price' => $item->total_price
                    ];
                })->toArray();
                
                // Get the most recent bill item timestamp (shows when last item was added)
                $latestItem = $bill->billItems->sortByDesc('service_date')->first();
                if ($latestItem && $latestItem->service_date) {
                    $mostRecentActivityTime = $latestItem->service_date;
                }
            }

            return (object) [
                'id' => 'TXN-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                'bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'created_at' => $mostRecentActivityTime, // Use most recent activity time
                'patient_name' => $patientName,
                'patient_id' => $patientId,
                'services' => $services,
                'total_amount' => $bill->total_amount,
                'payment_method' => ucfirst($bill->payment_method ?? 'cash'),
                'status' => ucfirst($bill->status),
                'paid_at' => $bill->paid_at,
                'cashier' => $bill->cashier_name ?? 'Staff'
            ];
        });

        // Apply search filter post-transformation if search term provided
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $transactions = $transactions->filter(function($transaction) use ($searchTerm) {
                $patientName = strtolower($transaction->patient_name);
                return str_contains($patientName, $searchTerm);
            });
        }

        // Update the collection in the paginator
        $bills->setCollection($transactions);

        return view('transactions.index', ['transactions' => $bills]);
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

        // Generate bill number
        $billNumber = MedicalBill::generateBillNumber();
        
        // Map payment method
        $paymentMethodMap = [
            'cash' => 'cash',
            'gcash' => 'gcash',
            'paymaya' => 'paymaya'
        ];
        $paymentMethod = $paymentMethodMap[$validated['payment_method']] ?? 'cash';

        // Get or create patient in POS database
        $localPatientId = 1; // Default for walk-ins
        if (!empty($validated['patient_id']) && $validated['patient_id'] !== 'WALK-IN') {
            // Check if patient exists in POS database
            $localPatient = \App\Models\Patient::find($validated['patient_id']);
            
            if (!$localPatient) {
                // Patient doesn't exist in POS, fetch from EMR and create
                try {
                    $emrPatient = $this->emrApiService->getPatient($validated['patient_id']);
                    
                    if ($emrPatient) {
                        // Create patient in POS database with same ID as EMR
                        $localPatient = new \App\Models\Patient([
                            'patient_code' => $emrPatient['patient_code'] ?? 'P' . str_pad($validated['patient_id'], 4, '0', STR_PAD_LEFT),
                            'first_name' => $emrPatient['first_name'] ?? '',
                            'last_name' => $emrPatient['last_name'] ?? '',
                            'middle_name' => $emrPatient['middle_name'] ?? '',
                            'date_of_birth' => $emrPatient['date_of_birth'] ?? now()->subYears(30)->format('Y-m-d'),
                            'gender' => strtolower($emrPatient['gender'] ?? 'other'),
                            'phone' => $emrPatient['phone'] ?? '',
                            'email' => $emrPatient['email'] ?? null,
                            'address' => $emrPatient['address'] ?? '',
                            'insurance_provider' => null,
                        ]);
                        
                        // Manually set the ID to match EMR
                        $localPatient->id = $validated['patient_id'];
                        $localPatient->save();
                        
                        $localPatientId = $localPatient->id;
                        Log::info('Created patient in POS database from EMR', ['patient_id' => $localPatientId]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to sync patient from EMR to POS', [
                        'patient_id' => $validated['patient_id'],
                        'error' => $e->getMessage()
                    ]);
                    // Fall back to default patient
                }
            } else {
                $localPatientId = $localPatient->id;
            }
        }

        // Create medical bill in database
        $bill = MedicalBill::create([
            'bill_number' => $billNumber,
            'patient_id' => $localPatientId,
            'subtotal' => $serviceTotal,
            'discount' => 0,
            'tax' => 0,
            'total_amount' => $serviceTotal,
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'notes' => $validated['notes'] . ' | Doctor: ' . $validated['assigned_doctor'] . ' | Time: ' . ($validated['appointment_time'] ?? 'Today, ' . date('g:i A')) . ' | Room: ' . ($validated['room_number'] ?? 'N/A'),
            'cashier_name' => 'Cashier Staff',
            'bill_date' => now(),
            'paid_at' => now(),
        ]);

        // Create bill items for each service
        foreach ($selectedServices as $service) {
            BillItem::create([
                'medical_bill_id' => $bill->id,
                'medical_service_id' => null,
                'service_name' => $service['name'],
                'service_category' => $this->getServiceCategory($service['name']),
                'quantity' => 1,
                'unit_price' => $service['price'],
                'total_price' => $service['price'],
                'notes' => null,
                'performed_by' => $validated['assigned_doctor'],
                'service_date' => now(),
            ]);
        }

        $transactionId = 'TXN-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT);
        
        // Also save to JSON file for backward compatibility (receipt generation)
        $transactionsFile = storage_path('app/transactions.json');
        $existingTransactions = [];
        if (file_exists($transactionsFile)) {
            $existingTransactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        }

        $receiptNumber = 'RCP-' . str_pad(count($existingTransactions) + 1, 4, '0', STR_PAD_LEFT);

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
            'cashier' => 'Cashier Staff',
            'receipt_number' => $receiptNumber,
            'notes' => $validated['notes'],
            'assigned_doctor' => $validated['assigned_doctor'],
            'appointment_time' => $validated['appointment_time'] ?? 'Today, ' . date('g:i A'),
            'room_number' => $validated['room_number'],
            'created_at' => now()->toISOString()
        ];

        $existingTransactions[] = $transaction;
        
        $storageDir = dirname($transactionsFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        file_put_contents($transactionsFile, json_encode($existingTransactions, JSON_PRETTY_PRINT));
        
        // Create appointment in EMR if patient has an ID (not walk-in)
        if (!empty($validated['patient_id']) && $validated['patient_id'] !== 'WALK-IN') {
            $this->createEmrAppointment($validated, $transactionId);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', "Transaction {$transactionId} processed successfully! Receipt printed.");
    }
    
    /**
     * Get service category based on service name
     */
    private function getServiceCategory(string $serviceName): string
    {
        $categories = [
            'Consultation' => 'consultation',
            'X-ray' => 'diagnostic',
            'ECG' => 'diagnostic',
            'Ultrasound' => 'diagnostic',
            'Pre-natal Package' => 'procedure',
            'Pre-Employment/Annual Medical Exam' => 'procedure',
            'Complete Blood Count (CBC)' => 'diagnostic',
            'Metabolic Panels' => 'diagnostic',
            'Lipid Panel' => 'diagnostic',
            'Thyroid Function Tests' => 'diagnostic',
            'Coagulation Panel' => 'diagnostic',
            'Enzyme Tests' => 'diagnostic',
            'Urinalysis' => 'diagnostic',
            'Microbiology Tests' => 'diagnostic',
            'Genetic Tests' => 'diagnostic',
            'Tumor Marker Tests' => 'diagnostic'
        ];
        
        return $categories[$serviceName] ?? 'consultation';
    }

    /**
     * Display specific transaction details.
     */
    public function show(string $id): View
    {
        // Extract bill ID from transaction ID format (TXN-0001 -> 1)
        $billId = (int) str_replace('TXN-', '', $id);
        
        // Get medical bill with related data
        $bill = MedicalBill::with(['billItems', 'patient'])->findOrFail($billId);
        
        // Get patient info
        $patientName = 'Unknown Patient';
        $patientId = 'N/A';
        
        if ($bill->patient) {
            $patientName = $bill->patient->full_name;
            $patientId = $bill->patient->id;
        } else {
            try {
                if ($bill->patient_id) {
                    $emrPatient = $this->emrApiService->getPatient($bill->patient_id);
                    if ($emrPatient) {
                        $patientName = $emrPatient['full_name'] ?? $emrPatient['first_name'] . ' ' . $emrPatient['last_name'];
                        $patientId = $emrPatient['id'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch patient from EMR', ['patient_id' => $bill->patient_id, 'error' => $e->getMessage()]);
            }
        }
        
        // Build services and medicines arrays
        $services = [];
        $medicines = [];
        $serviceTotal = 0;
        $medicineTotal = 0;
        $assignedDoctor = 'Not assigned';
        
        if ($bill->billItems && $bill->billItems->count() > 0) {
            foreach ($bill->billItems as $item) {
                if ($item->service_category === 'Medicine') {
                    $medicines[] = [
                        'name' => $item->service_name,
                        'quantity' => $item->quantity ?? 1,
                        'price' => $item->total_price / ($item->quantity ?? 1),
                        'total' => $item->total_price
                    ];
                    $medicineTotal += $item->total_price;
                } else {
                    $services[] = [
                        'name' => $item->service_name,
                        'price' => $item->total_price
                    ];
                    $serviceTotal += $item->total_price;
                }
                
                // Get doctor from first item's performed_by field
                if (empty($assignedDoctor) || $assignedDoctor === 'Not assigned') {
                    if ($item->performed_by) {
                        $assignedDoctor = $item->performed_by;
                    }
                }
            }
        }
        
        // Parse notes to extract doctor, time, and room information
        $appointmentTime = 'N/A';
        $roomNumber = 'N/A';
        $notes = $bill->notes ?? '';
        
        if (preg_match('/Doctor:\s*([^|]+)/', $notes, $matches)) {
            $assignedDoctor = trim($matches[1]);
        }
        if (preg_match('/Time:\s*([^|]+)/', $notes, $matches)) {
            $appointmentTime = trim($matches[1]);
        }
        if (preg_match('/Room:\s*(.+)$/', $notes, $matches)) {
            $roomNumber = trim($matches[1]);
        }
        
        // Clean up notes by removing the extracted metadata
        $cleanNotes = preg_replace('/\s*\|\s*Doctor:.*$/', '', $notes);
        
        // Transform to transaction object
        $transaction = (object) [
            'transaction_id' => 'TXN-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
            'receipt_number' => $bill->bill_number,
            'patient_name' => $patientName,
            'patient_id' => $patientId,
            'created_at' => $bill->bill_date ?? $bill->created_at,
            'cashier' => $bill->cashier_name ?? 'Staff',
            'status' => ucfirst($bill->status),
            'services' => $services,
            'service_total' => $serviceTotal,
            'medicines' => $medicines,
            'medicine_total' => $medicineTotal,
            'total_amount' => $bill->total_amount,
            'payment_method' => ucfirst($bill->payment_method ?? 'cash'),
            'amount_paid' => $bill->total_amount,
            'change_amount' => 0,
            'assigned_doctor' => $assignedDoctor,
            'appointment_time' => $appointmentTime,
            'room_number' => $roomNumber,
            'notes' => $cleanNotes
        ];

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Display receipt for printing.
     */
    public function receipt(string $receiptNumber)
    {
        // Get medical bill from database using receipt/bill number
        $bill = MedicalBill::where('bill_number', $receiptNumber)
            ->with(['billItems', 'patient'])
            ->first();
        
        if (!$bill) {
            abort(404, 'Receipt not found');
        }
        
        // Get patient info
        $patientName = 'Unknown Patient';
        $patientId = 'N/A';
        
        if ($bill->patient) {
            $patientName = $bill->patient->full_name;
            $patientId = $bill->patient->id;
        } else {
            try {
                if ($bill->patient_id) {
                    $emrPatient = $this->emrApiService->getPatient($bill->patient_id);
                    if ($emrPatient) {
                        $patientName = $emrPatient['full_name'] ?? $emrPatient['first_name'] . ' ' . $emrPatient['last_name'];
                        $patientId = $emrPatient['id'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch patient from EMR', ['patient_id' => $bill->patient_id, 'error' => $e->getMessage()]);
            }
        }
        
        // Build services and medicines arrays
        $services = [];
        $medicines = [];
        $assignedDoctor = 'Not assigned';
        
        if ($bill->billItems && $bill->billItems->count() > 0) {
            foreach ($bill->billItems as $item) {
                if ($item->service_category === 'Medicine') {
                    $medicines[] = [
                        'name' => $item->service_name,
                        'quantity' => $item->quantity ?? 1,
                        'price' => $item->total_price / ($item->quantity ?? 1),
                        'total' => $item->total_price
                    ];
                } else {
                    $services[] = [
                        'name' => $item->service_name,
                        'price' => $item->total_price
                    ];
                }
                
                // Get doctor from first item's performed_by field
                if (empty($assignedDoctor) || $assignedDoctor === 'Not assigned') {
                    if ($item->performed_by) {
                        $assignedDoctor = $item->performed_by;
                    }
                }
            }
        }
        
        // Parse notes to extract doctor, time, and room information
        $appointmentTime = 'N/A';
        $roomNumber = 'N/A';
        $notes = $bill->notes ?? '';
        
        if (preg_match('/Doctor:\s*([^|]+)/', $notes, $matches)) {
            $assignedDoctor = trim($matches[1]);
        }
        if (preg_match('/Time:\s*([^|]+)/', $notes, $matches)) {
            $appointmentTime = trim($matches[1]);
        }
        if (preg_match('/Room:\s*(.+)$/', $notes, $matches)) {
            $roomNumber = trim($matches[1]);
        }
        
        // Build transaction array for receipt template
        $transaction = [
            'transaction_id' => 'TXN-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
            'receipt_number' => $bill->bill_number,
            'patient_name' => $patientName,
            'patient_id' => $patientId,
            'created_at' => ($bill->bill_date ?? $bill->created_at)->toISOString(),
            'cashier' => $bill->cashier_name ?? 'Staff',
            'services' => $services,
            'medicines' => $medicines,
            'total_amount' => $bill->total_amount,
            'payment_method' => $bill->payment_method ?? 'cash',
            'amount_paid' => $bill->total_amount,
            'change_amount' => 0.00,
            'assigned_doctor' => $assignedDoctor,
            'appointment_time' => $appointmentTime,
            'room_number' => $roomNumber
        ];
        
        // Return raw HTML response to avoid any layout inheritance
        $html = view('transactions.receipt', compact('transaction'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Create basic appointment in EMR system after billing
     * This creates a scheduling record without medical details
     */
    private function createEmrAppointment(array $validatedData, string $transactionId): void
    {
        try {
            Log::info('POS: Processing EMR appointment for billing', [
                'patient_id' => $validatedData['patient_id'],
                'transaction_id' => $transactionId,
                'assigned_doctor' => $validatedData['assigned_doctor'] ?? 'Not assigned'
            ]);

            // Extract doctor name without specialty (e.g., "Patrick Mahomes - Radiology" -> "Patrick Mahomes")
            $assignedDoctor = $validatedData['assigned_doctor'] ?? 'Not assigned';
            $doctorNameOnly = $assignedDoctor;
            if (str_contains($assignedDoctor, ' - ')) {
                $doctorNameOnly = trim(explode(' - ', $assignedDoctor)[0]);
            }
            
            $patientId = $validatedData['patient_id'];
            $consultationDate = now()->toDateString();
            
            // Check if a consultation already exists for this patient today (created by medical staff)
            $existingConsultation = $this->emrApiService->findConsultationForDoctorAssignment(
                $patientId,
                $consultationDate
            );
            
            if ($existingConsultation) {
                // Consultation exists - assign doctor to it instead of creating new one
                Log::info('POS: Found existing consultation, assigning doctor', [
                    'consultation_id' => $existingConsultation['id'],
                    'patient_id' => $patientId,
                    'doctor_name' => $doctorNameOnly,
                    'transaction_id' => $transactionId
                ]);
                
                $assigned = $this->emrApiService->assignDoctorToConsultation(
                    $existingConsultation['id'],
                    $doctorNameOnly,
                    $transactionId
                );
                
                if ($assigned) {
                    Log::info('POS: Successfully assigned doctor to existing consultation', [
                        'consultation_id' => $existingConsultation['id'],
                        'transaction_id' => $transactionId
                    ]);
                } else {
                    Log::warning('POS: Failed to assign doctor to consultation', [
                        'consultation_id' => $existingConsultation['id'],
                        'transaction_id' => $transactionId
                    ]);
                }
                
                return; // Done - doctor assigned to existing consultation
            }
            
            // No existing consultation - create new one (walk-in patient scenario)
            Log::info('POS: No existing consultation found, creating new one', [
                'patient_id' => $patientId,
                'doctor_name' => $doctorNameOnly,
                'transaction_id' => $transactionId
            ]);
            
            $appointmentData = [
                'patient_id' => $patientId,
                'doctor_name' => $doctorNameOnly,
                'date' => $consultationDate,
                'status' => 'pending',
                'consultation_notes' => "Patient scheduled via billing. Transaction: {$transactionId}. Assigned to: " . $assignedDoctor,
                'bp' => '0/0',
                'temparature' => 0,
                'weight' => 0,
                'o2' => 0,
                'pr' => 0
            ];

            $response = $this->emrApiService->createConsultation($appointmentData);
            
            if ($response) {
                Log::info('POS: Successfully created new appointment in EMR', [
                    'consultation_id' => $response['id'] ?? 'unknown',
                    'transaction_id' => $transactionId
                ]);
            } else {
                Log::warning('POS: Failed to create appointment in EMR - no response', [
                    'transaction_id' => $transactionId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('POS: Error processing EMR appointment', [
                'error' => $e->getMessage(),
                'patient_id' => $validatedData['patient_id'],
                'transaction_id' => $transactionId
            ]);
        }
    }
}
