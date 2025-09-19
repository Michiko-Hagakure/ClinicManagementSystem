<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column based on clinic staff roles from interview
            $table->enum('role', [
                'clinic_staff',    // Info, Vital - administrative tasks, record viewing
                'medical_staff',   // Med tech, Rad tech, Ultrasound tech - perform tests, input results
                'doctor',          // Review and interpret results, consultations
                'cashier',         // Handle billing and pharmacy sales
                'owner'            // Full access, reports, system management
            ])->default('clinic_staff')->after('email');
            
            // Add department/specialty for medical staff
            $table->string('department')->nullable()->after('role'); // e.g., 'laboratory', 'radiology', 'ultrasound'
            
            // Add staff status
            $table->boolean('is_active')->default(true)->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'is_active']);
        });
    }
};
