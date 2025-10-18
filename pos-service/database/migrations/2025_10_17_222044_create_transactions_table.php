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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('patient_name');
            $table->string('patient_id')->nullable();
            $table->json('services')->nullable();
            $table->json('medicines')->nullable();
            $table->decimal('service_total', 10, 2)->default(0);
            $table->decimal('medicine_total', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method');
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->string('status')->default('Paid');
            $table->string('cashier');
            $table->string('receipt_number')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
