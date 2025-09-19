<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Clinic Staff',
            'email' => 'staff@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'clinic_staff',
        ]);

        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        User::create([
            'name' => 'Doctor',
            'email' => 'doctor@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);
    }
}
