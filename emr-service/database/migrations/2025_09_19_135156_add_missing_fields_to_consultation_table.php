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
        Schema::table('consultation', function (Blueprint $table) {
            // Add chief complaint column
            $table->text('chief_complaint')->nullable()->after('consultation_date');
            
            // Add vital signs columns
            $table->string('bp', 20)->nullable()->after('patient_instructions'); // Blood pressure
            $table->decimal('temparature', 4, 1)->nullable()->after('bp'); // Temperature 
            $table->decimal('weight', 5, 1)->nullable()->after('temparature'); // Weight in kg
            $table->integer('o2')->nullable()->after('weight'); // Oxygen saturation %
            $table->integer('pr')->nullable()->after('o2'); // Pulse rate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultation', function (Blueprint $table) {
            $table->dropColumn([
                'chief_complaint',
                'bp',
                'temparature', 
                'weight',
                'o2',
                'pr'
            ]);
        });
    }
};
