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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->id('audit_id');
            
            // What was changed
            $table->string('table_name'); // e.g., 'lab_result'
            $table->unsignedBigInteger('record_id'); // ID of the changed record
            $table->string('action'); // 'created', 'updated', 'deleted', 'state_changed'
            
            // Who made the change
            $table->unsignedBigInteger('user_id');
            $table->string('user_name'); // Store name for historical record
            $table->string('user_role'); // Store role at time of change
            
            // What specifically changed
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values  
            $table->json('changed_fields')->nullable(); // List of changed fields
            
            // Context information
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('reason')->nullable(); // Optional reason for change
            
            // Lab-specific audit fields
            $table->string('patient_id')->nullable(); // For lab records
            $table->string('status_from')->nullable(); // For workflow changes
            $table->string('status_to')->nullable(); // For workflow changes
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['table_name', 'record_id']);
            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['created_at']);
            $table->index(['patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail');
    }
};
