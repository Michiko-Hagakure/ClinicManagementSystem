<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * Create sample patients for testing the medical billing system.
     */
    public function run(): void
    {
        // Sample patients for testing
        Patient::create([
            'patient_code' => 'P0001',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'date_of_birth' => '1985-05-15',
            'gender' => 'female',
            'phone' => '0917-123-4567',
            'email' => 'maria.santos@email.com',
            'address' => 'Barangay 1, Quezon City, Metro Manila',
            'emergency_contact_name' => 'Jose Santos',
            'emergency_contact_phone' => '0918-123-4567',
            'is_active' => true,
        ]);

        Patient::create([
            'patient_code' => 'P0002',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'date_of_birth' => '1978-12-20',
            'gender' => 'male',
            'phone' => '0918-234-5678',
            'email' => 'juan.delacruz@email.com',
            'address' => 'Barangay 2, Marikina City, Metro Manila',
            'emergency_contact_name' => 'Ana Dela Cruz',
            'emergency_contact_phone' => '0919-234-5678',
            'is_active' => true,
        ]);

        Patient::create([
            'patient_code' => 'P0003',
            'first_name' => 'Ana',
            'last_name' => 'Rodriguez',
            'date_of_birth' => '1992-08-10',
            'gender' => 'female',
            'phone' => '0919-345-6789',
            'email' => 'ana.rodriguez@email.com',
            'address' => 'Barangay 1, Pasig City, Metro Manila',
            'emergency_contact_name' => 'Carlos Rodriguez',
            'emergency_contact_phone' => '0920-345-6789',
            'insurance_provider' => 'PhilHealth',
            'insurance_number' => 'PH123456789',
            'is_active' => true,
        ]);

        Patient::create([
            'patient_code' => 'P0004',
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'date_of_birth' => '1965-03-25',
            'gender' => 'male',
            'phone' => '0920-456-7890',
            'email' => 'carlos.mendoza@email.com',
            'address' => 'Barangay 3, Makati City, Metro Manila',
            'emergency_contact_name' => 'Elena Mendoza',
            'emergency_contact_phone' => '0921-456-7890',
            'insurance_provider' => 'Maxicare',
            'insurance_number' => 'MC987654321',
            'is_active' => true,
        ]);

        Patient::create([
            'patient_code' => 'P0005',
            'first_name' => 'Lisa',
            'last_name' => 'Garcia',
            'date_of_birth' => '1990-11-08',
            'gender' => 'female',
            'phone' => '0921-567-8901',
            'email' => 'lisa.garcia@email.com',
            'address' => 'Barangay 2, Taguig City, Metro Manila',
            'emergency_contact_name' => 'Miguel Garcia',
            'emergency_contact_phone' => '0922-567-8901',
            'is_active' => true,
        ]);

        echo "✅ Sample patients seeded successfully!\n";
        echo "Created " . Patient::count() . " patient records for testing.\n";
    }
}
