<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalBill;
use App\Models\BillItem;
use App\Models\Patient;
use App\Models\MedicalService;
use App\Models\Payment;
use App\Services\EmrApiService;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    private EmrApiService $emrApiService;

    public function __construct(EmrApiService $emrApiService)
    {
        $this->emrApiService = $emrApiService;
    }

    /**
     * Display the billing interface
     */
    public function index()
    {
        $recentBills = MedicalBill::with('patient', 'items.medicalService')
            ->latest()
            ->take(10)
            ->get();

        return view('billing.create', compact('recentBills'));
    }

    /**
     * Show the form for creating a new medical bill
     */
    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        $services = MedicalService::orderBy('category')
            ->orderBy('name')
            ->get();

        return view('billing.create', compact('patients', 'services'));
    }

    /**
     * Store a newly created medical bill
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'cashier_name' => 'required|string|max:100',
            'payment_method' => 'required|in:cash,credit_card,gcash,paymaya,insurance',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:medical_services,id',
            'services.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create the medical bill
            $medicalBill = MedicalBill::create([
                'bill_number' => MedicalBill::generateBillNumber(),
                'patient_id' => $request->patient_id,
                'cashier_name' => $request->cashier_name,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'bill_date' => now(),
                'subtotal' => 0, // Will be calculated below
                'total_amount' => 0, // Will be calculated below
            ]);

            $totalAmount = 0;

            // Add bill items
            foreach ($request->services as $serviceData) {
                $service = MedicalService::find($serviceData['service_id']);
                $quantity = $serviceData['quantity'];
                $totalPrice = $service->price * $quantity;

                BillItem::create([
                    'medical_bill_id' => $medicalBill->id,
                    'medical_service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_category' => $service->category,
                    'quantity' => $quantity,
                    'unit_price' => $service->price,
                    'total_price' => $totalPrice,
                ]);

                $totalAmount += $totalPrice;
            }

            // Update the totals
            $medicalBill->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount
            ]);

            // Create initial payment record if paid
            if ($request->payment_method !== 'insurance') {
                Payment::create([
                    'medical_bill_id' => $medicalBill->id,
                    'payment_reference' => Payment::generatePaymentReference(),
                    'amount' => $totalAmount,
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                    'payment_date' => now(),
                ]);

                $medicalBill->update(['status' => 'paid']);
            }

            DB::commit();

            return redirect()->route('billing.show', $medicalBill)
                ->with('success', 'Medical bill created successfully! Bill Number: ' . $medicalBill->bill_number);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create medical bill: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified medical bill
     */
    public function show(MedicalBill $medicalBill)
    {
        $medicalBill->load('patient', 'items.medicalService', 'payments');
        
        return view('billing.show', compact('medicalBill'));
    }

    /**
     * Show all medical bills (invoices list)
     */
    public function invoices(Request $request)
    {
        $query = MedicalBill::with('patient', 'items');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('bill_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('bill_date', '<=', $request->date_to);
        }

        // Search by bill number or patient name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $bills = $query->latest('bill_date')->paginate(20);

        return view('billing.invoices', compact('bills'));
    }

    /**
     * Get services by category (AJAX endpoint)
     */
    public function getServicesByCategory(Request $request)
    {
        $category = $request->category;
        
        $services = MedicalService::where('category', $category)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return response()->json($services);
    }

    /**
     * Search patients (AJAX endpoint) - Uses EMR service
     */
    public function searchPatients(Request $request)
    {
        $search = $request->search;
        
        if (empty($search) || strlen($search) < 2) {
            return response()->json([]);
        }

        // Try to get patients from EMR service
        $patients = $this->emrApiService->searchPatients($search);

        // If EMR service is unavailable, fallback to local patients (if any)
        if (empty($patients) && !$this->emrApiService->isEmrServiceAvailable()) {
            $localPatients = Patient::where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('patient_code', 'like', "%{$search}%")
                ->limit(10)
                ->get(['id', 'patient_code', 'first_name', 'last_name'])
                ->map(function ($patient) {
                    return [
                        'id' => $patient->id,
                        'patient_code' => $patient->patient_code,
                        'first_name' => $patient->first_name,
                        'last_name' => $patient->last_name,
                        'full_name' => $patient->first_name . ' ' . $patient->last_name,
                    ];
                });

            $patients = $localPatients->toArray();
        }

        return response()->json($patients);
    }

    /**
     * Get patient details by ID from EMR service (AJAX endpoint)
     */
    public function getPatientDetails(Request $request, $patientId)
    {
        // Try to get patient from EMR service
        $patient = $this->emrApiService->getPatient($patientId);

        if ($patient) {
            return response()->json($patient);
        }

        // If EMR service is unavailable, fallback to local patient
        if (!$this->emrApiService->isEmrServiceAvailable()) {
            $localPatient = Patient::find($patientId);
            
            if ($localPatient) {
                return response()->json([
                    'id' => $localPatient->id,
                    'patient_code' => $localPatient->patient_code,
                    'first_name' => $localPatient->first_name,
                    'last_name' => $localPatient->last_name,
                    'full_name' => $localPatient->first_name . ' ' . $localPatient->last_name,
                    'date_of_birth' => $localPatient->date_of_birth,
                    'gender' => $localPatient->gender,
                    'phone' => $localPatient->phone,
                    'address' => $localPatient->address,
                    'insurance_provider' => $localPatient->insurance_provider,
                ]);
            }
        }

        return response()->json(['error' => 'Patient not found'], 404);
    }

    /**
     * Print bill receipt
     */
    public function printBill(MedicalBill $medicalBill)
    {
        $medicalBill->load('patient', 'items.medicalService', 'payments');
        
        return view('billing.print', compact('medicalBill'));
    }
}
