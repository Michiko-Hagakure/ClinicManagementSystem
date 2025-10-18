<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for the clinic system
        $users = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'doctor@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'doctor',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'pharmacy@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'pharmacy_staff',
            ],
            [
                'name' => 'James Wilson',
                'email' => 'pharmacist@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'cashier@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ],
            [
                'name' => 'Mary Angels',
                'email' => 'owner@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ],
            [
                'name' => 'Anna Rodriguez',
                'email' => 'staff@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'clinic_staff',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'medical@clinic.test',
                'password' => Hash::make('password'),
                'role' => 'medical_staff',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Check by email
                $userData // Create or update with this data
            );
        }

        $this->command->info('Created ' . count($users) . ' test users with the following credentials:');
        $this->command->info('');
        $this->command->info('🏥 SYSTEM ACCESS:');
        $this->command->info('Doctor Portal: doctor@clinic.test / password');
        $this->command->info('Pharmacy Staff: pharmacy@clinic.test / password');
        $this->command->info('Pharmacist: pharmacist@clinic.test / password');
        $this->command->info('Clinic Owner: owner@clinic.test / password');
        $this->command->info('Cashier: cashier@clinic.test / password');
        $this->command->info('Clinic Staff: staff@clinic.test / password');
        $this->command->info('Medical Staff: medical@clinic.test / password');
        $this->command->info('');
        $this->command->info('🔐 All passwords: password');
        $this->command->info('');
        $this->command->info('📱 SERVICE REDIRECTS:');
        $this->command->info('• Pharmacy Staff/Pharmacist → Inventory System (Port 8002)');
        $this->command->info('• Clinic Owner → Inventory Reports (Port 8002)');
        $this->command->info('• Doctor → Doctor Portal (Port 8000)');
        $this->command->info('• Cashier → POS System (Port 8001)');
        $this->command->info('• Staff → EMR System (Port 8000)');
    }
}
