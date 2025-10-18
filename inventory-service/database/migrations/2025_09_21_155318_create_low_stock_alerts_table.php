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
        Schema::create('low_stock_alerts', function (Blueprint $table) {
            $table->id('alert_id');
            $table->unsignedBigInteger('medicine_id');
            $table->datetime('alert_date');
            $table->integer('threshold');
            $table->timestamps();

            $table->foreign('medicine_id')->references('medicine_id')->on('medicine')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('low_stock_alerts');
    }
};
