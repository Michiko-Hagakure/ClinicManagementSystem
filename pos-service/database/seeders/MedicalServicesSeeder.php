<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalService;

class MedicalServicesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * Populate medical services based on Mary Angels Diagnostic Clinic documentation.
     */
    public function run(): void
    {
        // Consultation Services
        MedicalService::create([
            'code' => 'CONS-001',
            'name' => 'General Consultation',
            'description' => 'General medical consultation with our experienced doctors. Includes basic health assessment and medical advice.',
            'category' => 'consultation',
            'subcategory' => 'general',
            'price' => 500.00,
            'estimated_duration' => 30,
            'is_active' => true,
            'department' => 'General Medicine',
        ]);

        MedicalService::create([
            'code' => 'CONS-002',
            'name' => 'Specialist Consultation',
            'description' => 'Specialized medical consultation for specific health conditions requiring expert medical attention.',
            'category' => 'consultation',
            'subcategory' => 'specialist',
            'price' => 800.00,
            'estimated_duration' => 45,
            'is_active' => true,
            'department' => 'Specialist Medicine',
        ]);

        MedicalService::create([
            'code' => 'CONS-003',
            'name' => 'Follow-up Visit',
            'description' => 'Follow-up consultation for ongoing treatment monitoring and medical care continuity.',
            'category' => 'consultation',
            'subcategory' => 'follow-up',
            'price' => 300.00,
            'estimated_duration' => 20,
            'is_active' => true,
            'department' => 'General Medicine',
        ]);

        // Diagnostic Services - Laboratory
        MedicalService::create([
            'code' => 'LAB-001',
            'name' => 'Laboratory Tests',
            'description' => 'Blood work, urine tests, and other laboratory analyses for medical diagnosis.',
            'category' => 'diagnostic',
            'subcategory' => 'laboratory',
            'price' => 450.00,
            'estimated_duration' => 45,
            'is_active' => true,
            'department' => 'Laboratory',
            'preparation_notes' => 'Fasting may be required for some tests. Please confirm with staff.',
        ]);

        // Diagnostic Services - Radiology
        MedicalService::create([
            'code' => 'XRAY-001',
            'name' => 'X-ray Imaging',
            'description' => 'Digital radiological imaging for bone and organ assessment using advanced X-ray technology.',
            'category' => 'diagnostic',
            'subcategory' => 'radiology',
            'price' => 650.00,
            'estimated_duration' => 20,
            'is_active' => true,
            'department' => 'Radiology',
            'preparation_notes' => 'Remove metal objects before examination.',
        ]);

        MedicalService::create([
            'code' => 'US-001',
            'name' => 'Ultrasound',
            'description' => 'Non-invasive imaging using sound waves for detailed visualization of internal organs.',
            'category' => 'diagnostic',
            'subcategory' => 'imaging',
            'price' => 1200.00,
            'estimated_duration' => 30,
            'is_active' => true,
            'department' => 'Radiology',
            'preparation_notes' => 'Full bladder may be required for pelvic ultrasounds.',
        ]);

        MedicalService::create([
            'code' => 'CT-001',
            'name' => 'CT Scan',
            'description' => 'Computed Tomography for detailed cross-sectional imaging of the body.',
            'category' => 'diagnostic',
            'subcategory' => 'advanced_imaging',
            'price' => 3500.00,
            'estimated_duration' => 45,
            'is_active' => true,
            'department' => 'Advanced Imaging',
            'preparation_notes' => 'Contrast material may be required. Inform staff of any allergies.',
        ]);

        MedicalService::create([
            'code' => 'MRI-001',
            'name' => 'MRI',
            'description' => 'Magnetic Resonance Imaging for detailed soft tissue imaging using magnetic fields.',
            'category' => 'diagnostic',
            'subcategory' => 'advanced_imaging',
            'price' => 8000.00,
            'estimated_duration' => 60,
            'is_active' => true,
            'department' => 'Advanced Imaging',
            'preparation_notes' => 'Remove all metal objects. Inform staff of any implants or claustrophobia.',
        ]);

        // Diagnostic Services - Cardiology
        MedicalService::create([
            'code' => 'ECG-001',
            'name' => 'ECG/EKG',
            'description' => 'Electrocardiogram for heart function assessment and cardiac rhythm analysis.',
            'category' => 'diagnostic',
            'subcategory' => 'cardiology',
            'price' => 350.00,
            'estimated_duration' => 15,
            'is_active' => true,
            'department' => 'Cardiology',
            'preparation_notes' => 'Avoid caffeine 2 hours before test.',
        ]);

        // Sample Medications (basic examples)
        MedicalService::create([
            'code' => 'MED-001',
            'name' => 'Paracetamol 500mg',
            'description' => 'Pain reliever and fever reducer. 30 tablets per box.',
            'category' => 'medication',
            'subcategory' => 'analgesic',
            'price' => 30.00,
            'is_active' => true,
            'department' => 'Pharmacy',
        ]);

        MedicalService::create([
            'code' => 'MED-002',
            'name' => 'Vitamin C 1000mg',
            'description' => 'Vitamin C supplement for immune system support. 100 tablets per bottle.',
            'category' => 'medication',
            'subcategory' => 'vitamin',
            'price' => 150.00,
            'is_active' => true,
            'department' => 'Pharmacy',
        ]);

        MedicalService::create([
            'code' => 'MED-003',
            'name' => 'Betadine Solution',
            'description' => 'Antiseptic solution for wound care and disinfection. 60ml bottle.',
            'category' => 'medication',
            'subcategory' => 'antiseptic',
            'price' => 80.00,
            'is_active' => true,
            'department' => 'Pharmacy',
        ]);

        MedicalService::create([
            'code' => 'SUP-001',
            'name' => 'Face Mask (Box)',
            'description' => 'Disposable medical face masks for protection. 50 pieces per box.',
            'category' => 'medication',
            'subcategory' => 'supplies',
            'price' => 200.00,
            'is_active' => true,
            'department' => 'Pharmacy',
        ]);

        MedicalService::create([
            'code' => 'SUP-002',
            'name' => 'Alcohol 70%',
            'description' => 'Rubbing alcohol for disinfection and sanitization. 500ml bottle.',
            'category' => 'medication',
            'subcategory' => 'supplies',
            'price' => 40.00,
            'is_active' => true,
            'department' => 'Pharmacy',
        ]);

        echo "✅ Medical services seeded successfully!\n";
        echo "+------------------+-------+-----------------------+\n";
        echo "| Category         | Count | Price Range           |\n";
        echo "+------------------+-------+-----------------------+\n";
        echo "| Consultations    | 3     | ₱300 - ₱800          |\n";
        echo "| Diagnostics      | 6     | ₱350 - ₱8,000        |\n";
        echo "| Medications      | 5     | ₱30 - ₱200           |\n";
        echo "+------------------+-------+-----------------------+\n";
        echo "Total services: " . MedicalService::count() . "\n";
    }
}
