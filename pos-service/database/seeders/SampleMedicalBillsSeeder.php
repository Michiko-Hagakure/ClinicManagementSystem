<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalBill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\MedicalService;
use Carbon\Carbon;

class SampleMedicalBillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available patients and services
        $patients = Patient::all();
        $services = MedicalService::all();

        if ($patients->isEmpty() || $services->isEmpty()) {
            $this->command->error('Please run PatientsSeeder and MedicalServicesSeeder first!');
            return;
        }

        // Create sample medical bills for today and past few days
        $this->createTodaysBills($patients, $services);
        $this->createRecentBills($patients, $services);
        
        $this->command->info('✅ Sample medical bills created successfully!');
        $this->command->info('Created bills for dashboard testing with both paid and pending statuses.');
    }

    /**
     * Create bills for today
     */
    private function createTodaysBills($patients, $services)
    {
        $today = Carbon::now();
        
        // Morning bills (paid)
        $this->createBill($patients->random(), $services, $today->copy()->setTime(9, 15), 'paid', 'cash');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(9, 45), 'paid', 'credit_card');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(10, 20), 'paid', 'cash');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(10, 50), 'paid', 'gcash');
        
        // Afternoon bills (mix of paid and pending)
        $this->createBill($patients->random(), $services, $today->copy()->setTime(14, 15), 'paid', 'paymaya');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(14, 45), 'pending', 'insurance');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(15, 30), 'paid', 'cash');
        $this->createBill($patients->random(), $services, $today->copy()->setTime(16, 10), 'pending', 'credit_card');
    }

    /**
     * Create bills for recent days
     */
    private function createRecentBills($patients, $services)
    {
        // Yesterday
        $yesterday = Carbon::yesterday();
        for ($i = 0; $i < 5; $i++) {
            $time = $yesterday->copy()->addHours(rand(9, 17))->addMinutes(rand(0, 59));
            $this->createBill($patients->random(), $services, $time, 'paid', ['cash', 'credit_card', 'gcash'][rand(0, 2)]);
        }

        // Day before yesterday
        $dayBefore = Carbon::yesterday()->subDay();
        for ($i = 0; $i < 4; $i++) {
            $time = $dayBefore->copy()->addHours(rand(9, 17))->addMinutes(rand(0, 59));
            $this->createBill($patients->random(), $services, $time, 'paid', ['cash', 'credit_card'][rand(0, 1)]);
        }

        // This week
        for ($i = 0; $i < 8; $i++) {
            $date = Carbon::now()->subDays(rand(3, 6));
            $time = $date->copy()->addHours(rand(9, 17))->addMinutes(rand(0, 59));
            $status = rand(0, 10) > 8 ? 'pending' : 'paid'; // 80% paid, 20% pending
            $paymentMethod = $status === 'pending' ? 'insurance' : ['cash', 'credit_card', 'gcash', 'paymaya'][rand(0, 3)];
            $this->createBill($patients->random(), $services, $time, $status, $paymentMethod);
        }
    }

    /**
     * Create a single medical bill with items
     */
    private function createBill($patient, $services, $billDate, $status, $paymentMethod)
    {
        // Create the medical bill
        $bill = MedicalBill::create([
            'bill_number' => MedicalBill::generateBillNumber(),
            'patient_id' => $patient->id,
            'cashier_name' => ['Maria Santos', 'Juan Reyes', 'Ana Garcia', 'Carlos Lopez'][rand(0, 3)],
            'payment_method' => $paymentMethod,
            'status' => $status,
            'bill_date' => $billDate,
            'subtotal' => 0, // Will be calculated
            'total_amount' => 0, // Will be calculated
        ]);

        // Add 1-4 services to the bill
        $numServices = rand(1, 4);
        $selectedServices = $services->random($numServices);
        $totalAmount = 0;

        foreach ($selectedServices as $service) {
            $quantity = $service->category === 'medication' ? rand(1, 3) : 1;
            $unitPrice = $service->price;
            $totalPrice = $unitPrice * $quantity;

            BillItem::create([
                'medical_bill_id' => $bill->id,
                'medical_service_id' => $service->id,
                'service_name' => $service->name,
                'service_category' => $service->category,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            $totalAmount += $totalPrice;
        }

        // Update bill totals
        $bill->update([
            'subtotal' => $totalAmount,
            'total_amount' => $totalAmount
        ]);

        // Create payment record if paid
        if ($status === 'paid') {
            Payment::create([
                'medical_bill_id' => $bill->id,
                'payment_reference' => Payment::generatePaymentReference(),
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'payment_date' => $billDate,
            ]);
        }
    }
}
