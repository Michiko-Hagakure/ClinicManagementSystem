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
        Schema::create('medical_services', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Service code (e.g., CONS-001, XRAY-001)
            $table->string('name'); // Service name
            $table->text('description')->nullable();
            $table->enum('category', ['consultation', 'diagnostic', 'medication', 'procedure']);
            $table->string('subcategory')->nullable(); // e.g., laboratory, radiology, cardiology
            $table->decimal('price', 10, 2);
            $table->integer('estimated_duration')->nullable(); // Duration in minutes
            $table->boolean('is_active')->default(true);
            $table->string('department')->nullable();
            $table->text('preparation_notes')->nullable(); // Special preparation instructions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_services');
    }
};
