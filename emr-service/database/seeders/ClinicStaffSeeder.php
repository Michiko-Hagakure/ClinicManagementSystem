<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClinicStaffSeeder extends Seeder
{
    /**
     * Seed the clinic staff based on Mary Angels Diagnostic Clinic roles from interview.
     * Creates staff for each role type identified in documentation.
     */
    public function run(): void
    {
        // Owner - full system access
        User::create([
            'name' => 'Mary Angels Clinic Owner',
            'email' => 'owner@maryangels.clinic',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'department' => null,
            'is_active' => true,
        ]);

        // Doctors - can review and interpret results
        User::create([
            'name' => 'Dr. Juan Santos',
            'email' => 'doctor@maryangels.clinic',
            'password' => Hash::make('doctor123'),
            'role' => 'doctor',
            'department' => 'general',
            'is_active' => true,
        ]);

        // Medical Staff - perform tests and input results
        User::create([
            'name' => 'Maria Laboratory Tech',
            'email' => 'labtech@maryangels.clinic',
            'password' => Hash::make('medtech123'),
            'role' => 'medical_staff',
            'department' => 'laboratory',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Carlos Radiology Tech',
            'email' => 'radtech@maryangels.clinic',
            'password' => Hash::make('radtech123'),
            'role' => 'medical_staff',
            'department' => 'radiology',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Ana Ultrasound Tech',
            'email' => 'ultratech@maryangels.clinic',
            'password' => Hash::make('ultratech123'),
            'role' => 'medical_staff',
            'department' => 'ultrasound',
            'is_active' => true,
        ]);

        // Clinic Staff - administrative, record viewing
        User::create([
            'name' => 'Rosa Information Staff',
            'email' => 'info@maryangels.clinic',
            'password' => Hash::make('clinic123'),
            'role' => 'clinic_staff',
            'department' => 'information',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Pedro Vital Signs',
            'email' => 'vital@maryangels.clinic',
            'password' => Hash::make('vital123'),
            'role' => 'clinic_staff',
            'department' => 'vital_signs',
            'is_active' => true,
        ]);

        // Cashier - billing and pharmacy
        User::create([
            'name' => 'Lisa Cashier',
            'email' => 'cashier@maryangels.clinic',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
            'department' => 'billing',
            'is_active' => true,
        ]);

        $this->command->info('✅ Clinic staff seeded successfully!');
        $this->command->table(
            ['Role', 'Count', 'Example Email'],
            [
                ['Owner', '1', 'owner@maryangels.clinic'],
                ['Doctor', '1', 'doctor@maryangels.clinic'],
                ['Medical Staff', '3', 'labtech@maryangels.clinic'],
                ['Clinic Staff', '2', 'info@maryangels.clinic'],
                ['Cashier', '1', 'cashier@maryangels.clinic'],
            ]
        );
        $this->command->info('Default password for all users: [role]123 (e.g., owner123, doctor123)');
    }
}
