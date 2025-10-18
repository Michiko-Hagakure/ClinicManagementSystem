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
        Schema::create('dispense_medicine', function (Blueprint $table) {
            $table->id('dispense_id');
            $table->integer('patient_id')->nullable();
            $table->unsignedBigInteger('medicine_id');
            $table->integer('quantity');
            $table->datetime('date');
            $table->timestamps();

            $table->foreign('medicine_id')->references('medicine_id')->on('medicine')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispense_medicine');
    }
};
