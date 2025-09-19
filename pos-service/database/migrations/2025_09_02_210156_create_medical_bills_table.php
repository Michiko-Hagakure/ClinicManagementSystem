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
        Schema::create('medical_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique(); // Bill/Invoice number
            $table->unsignedBigInteger('patient_id'); // Will add foreign key later
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'partially_paid', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'credit_card', 'gcash', 'paymaya', 'insurance'])->nullable();
            $table->text('notes')->nullable();
            $table->string('cashier_name')->nullable();
            $table->datetime('bill_date');
            $table->datetime('due_date')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_bills');
    }
};
