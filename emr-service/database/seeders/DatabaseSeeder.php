<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed clinic staff based on Mary Angels Diagnostic Clinic interview
        $this->call([
            UserSeeder::class,
            ClinicStaffSeeder::class,
        ]);
    }
}
