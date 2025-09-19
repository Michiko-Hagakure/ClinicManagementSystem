<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create sample users for testing
        $users = [
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'doctor@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nurse Jane Cruz',
                'email' => 'staff@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'clinic_staff',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Cashier John Reyes',
                'email' => 'cashier@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin User (Owner)',
                'email' => 'admin@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Robert Garcia',
                'email' => 'doctor2@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Cashier Sarah Lopez',
                'email' => 'cashier2@clinic.com',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Check by email
                $userData // Create or update with this data
            );
        }
    }
}
