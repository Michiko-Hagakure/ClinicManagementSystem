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
        Schema::table('lab_results', function (Blueprint $table) {
            // Add fields for doctor uploads and file attachments
            $table->string('test_type')->nullable()->after('test_name'); // Alternative to test_name for flexibility
            $table->text('results')->nullable()->after('result'); // Alternative to result for longer text
            $table->json('file_attachments')->nullable()->after('results'); // Store uploaded file names
            $table->text('doctor_notes')->nullable()->after('notes'); // Doctor's interpretation separate from technician notes
            $table->timestamp('reviewed_at')->nullable()->after('doctor_notes'); // When reviewed by doctor
            $table->string('reviewed_by')->nullable()->after('reviewed_at'); // Which doctor reviewed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->dropColumn([
                'test_type',
                'results', 
                'file_attachments',
                'doctor_notes',
                'reviewed_at',
                'reviewed_by'
            ]);
        });
    }
};