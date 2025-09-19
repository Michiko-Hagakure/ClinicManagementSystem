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
        Schema::table('medical_bills', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->foreign('medical_bill_id')->references('id')->on('medical_bills')->onDelete('cascade');
            $table->foreign('medical_service_id')->references('id')->on('medical_services');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('medical_bill_id')->references('id')->on('medical_bills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_bills', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropForeign(['medical_bill_id']);
            $table->dropForeign(['medical_service_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['medical_bill_id']);
        });
    }
};
