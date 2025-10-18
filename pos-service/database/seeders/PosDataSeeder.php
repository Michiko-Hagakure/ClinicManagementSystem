<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\MedicalBill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\MedicalService;
use Carbon\Carbon;

class PosDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample patients
        $patients = [
            [
                'patient_code' => 'P0001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'middle_name' => 'Cruz',
                'date_of_birth' => '1985-06-15',
                'gender' => 'female',
                'phone' => '09171234567',
                'email' => 'maria.santos@email.com',
                'address' => '123 Main St, Manila',
                'is_active' => true,
            ],
            [
                'patient_code' => 'P0002',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Garcia',
                'date_of_birth' => '1978-03-22',
                'gender' => 'male',
                'phone' => '09189876543',
                'email' => 'juan.delacruz@email.com',
                'address' => '456 Oak Ave, Quezon City',
                'is_active' => true,
            ],
            [
                'patient_code' => 'P0003',
                'first_name' => 'Ana',
                'last_name' => 'Rodriguez',
                'middle_name' => 'Lopez',
                'date_of_birth' => '1992-11-08',
                'gender' => 'female',
                'phone' => '09156789012',
                'email' => 'ana.rodriguez@email.com',
                'address' => '789 Pine Rd, Makati',
                'is_active' => true,
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        // Create sample medical services
        $services = [
            ['code' => 'CONS-001', 'name' => 'General Consultation', 'category' => 'consultation', 'price' => 500.00],
            ['code' => 'XRAY-001', 'name' => 'X-ray', 'category' => 'diagnostic', 'price' => 800.00],
            ['code' => 'LAB-001', 'name' => 'Blood Test', 'category' => 'diagnostic', 'price' => 350.00],
            ['code' => 'ECG-001', 'name' => 'ECG', 'category' => 'diagnostic', 'price' => 450.00],
            ['code' => 'ULTRA-001', 'name' => 'Ultrasound', 'category' => 'diagnostic', 'price' => 1200.00],
            ['code' => 'MED-001', 'name' => 'Paracetamol', 'category' => 'medication', 'price' => 25.00],
            ['code' => 'MED-002', 'name' => 'Amoxicillin', 'category' => 'medication', 'price' => 150.00],
        ];

        foreach ($services as $serviceData) {
            MedicalService::create($serviceData);
        }

        // Create sample bills for today and recent days
        $today = Carbon::today();
        $patients = Patient::all();
        $services = MedicalService::all();

        // Today's transactions
        for ($i = 1; $i <= 5; $i++) {
            $patient = $patients->random();
            $bill = MedicalBill::create([
                'bill_number' => MedicalBill::generateBillNumber(),
                'patient_id' => $patient->id,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'status' => 'paid',
                'payment_method' => collect(['cash', 'gcash', 'credit_card'])->random(),
                'cashier_name' => 'Cashier Staff',
                'bill_date' => $today->copy()->addHours(rand(8, 16)),
                'paid_at' => $today->copy()->addHours(rand(8, 16)),
            ]);

            // Add random services to the bill
            $selectedServices = $services->random(rand(1, 3));
            $subtotal = 0;

            foreach ($selectedServices as $service) {
                $quantity = rand(1, 2);
                $totalPrice = $service->price * $quantity;
                $subtotal += $totalPrice;

                BillItem::create([
                    'medical_bill_id' => $bill->id,
                    'medical_service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_category' => $service->category,
                    'quantity' => $quantity,
                    'unit_price' => $service->price,
                    'total_price' => $totalPrice,
                    'service_date' => $bill->bill_date,
                ]);
            }

            // Update bill totals
            $bill->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            // Create payment record
            Payment::create([
                'medical_bill_id' => $bill->id,
                'payment_reference' => Payment::generatePaymentReference(),
                'amount' => $bill->total_amount,
                'payment_method' => $bill->payment_method,
                'status' => 'completed',
                'processed_by' => 'Cashier Staff',
                'payment_date' => $bill->paid_at,
                'change_amount' => $bill->payment_method === 'cash' ? rand(0, 100) : 0,
            ]);
        }

        // Create some pending bills
        for ($i = 1; $i <= 3; $i++) {
            $patient = $patients->random();
            $bill = MedicalBill::create([
                'bill_number' => MedicalBill::generateBillNumber(),
                'patient_id' => $patient->id,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'status' => 'pending',
                'cashier_name' => 'Cashier Staff',
                'bill_date' => $today->copy()->addHours(rand(8, 16)),
                'due_date' => $today->copy()->addDays(3),
            ]);

            // Add services
            $selectedServices = $services->random(rand(1, 2));
            $subtotal = 0;

            foreach ($selectedServices as $service) {
                $quantity = 1;
                $totalPrice = $service->price * $quantity;
                $subtotal += $totalPrice;

                BillItem::create([
                    'medical_bill_id' => $bill->id,
                    'medical_service_id' => $service->id,
                    'service_name' => $service->name,
                    'service_category' => $service->category,
                    'quantity' => $quantity,
                    'unit_price' => $service->price,
                    'total_price' => $totalPrice,
                    'service_date' => $bill->bill_date,
                ]);
            }

            $bill->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);
        }
    }
}