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
        Schema::create('consultation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients');
            $table->date('consultation_date');
            $table->text('consultation_notes')->nullable();
            $table->text('assessment')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('medications_prescribed')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['completed', 'pending', 'follow_up_required'])->default('pending');
            $table->string('referred_to')->nullable();
            $table->text('patient_history_notes')->nullable();
            $table->text('physical_examination')->nullable();
            $table->text('patient_instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation');
    }
};
