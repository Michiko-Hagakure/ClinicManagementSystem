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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_bill_id'); // Will add foreign key later
            $table->string('payment_reference')->unique(); // Payment reference number
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'gcash', 'paymaya', 'insurance']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable(); // External transaction ID
            $table->text('notes')->nullable();
            $table->string('processed_by')->nullable(); // Cashier name
            $table->datetime('payment_date');
            $table->decimal('change_amount', 10, 2)->default(0); // For cash payments
            $table->json('payment_details')->nullable(); // Store additional payment info
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
