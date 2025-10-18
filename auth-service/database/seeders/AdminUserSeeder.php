<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'Administration',
                'is_active' => true,
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@clinic.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the password after first login!');
    }
}

