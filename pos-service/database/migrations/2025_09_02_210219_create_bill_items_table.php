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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_bill_id'); // Will add foreign key later
            $table->unsignedBigInteger('medical_service_id'); // Will add foreign key later
            $table->string('service_name'); // Store service name for historical reference
            $table->string('service_category'); // consultation, diagnostic, medication
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable(); // Special notes for this service
            $table->string('performed_by')->nullable(); // Doctor/technician name
            $table->datetime('service_date')->nullable(); // When service was performed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
