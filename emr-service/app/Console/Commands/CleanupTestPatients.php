<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\LabResult;
use Illuminate\Console\Command;

class CleanupTestPatients extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'patients:cleanup-test {--dry-run : Show what would be deleted without actually deleting} {--force : Skip confirmation} {--all : Delete ALL patients}';

    /**
     * The console command description.
     */
    protected $description = 'Remove test/dummy patient records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->warn('⚠️  ALL MODE: This will delete ALL patients in the database!');
            $this->newLine();
            $testPatients = Patient::all();
        } else {
            $this->info('🔍 Scanning for test/dummy patient records...');
            $this->newLine();

            // List of test patient names
            $testNames = [
                'Test Patient',
                'John Doe',
                'Jane Doe',
                'Test',
                'Demo',
                'Sample Patient',
            ];

            // Find test patients
            $testPatients = Patient::where(function($query) use ($testNames) {
                    $query->whereIn('first_name', $testNames)
                        ->orWhereIn('last_name', $testNames)
                        ->orWhere('first_name', 'LIKE', 'Test%')
                        ->orWhere('first_name', 'LIKE', 'Demo%')
                        ->orWhere('first_name', 'LIKE', 'Sample%')
                        ->orWhere('last_name', 'LIKE', 'Test%')
                        ->orWhere('last_name', 'LIKE', 'Demo%')
                        ->orWhere('last_name', 'LIKE', 'Sample%');
                })
                ->get();
        }

        if ($testPatients->isEmpty()) {
            $this->info('✅ No test patients found!');
            return 0;
        }

        $this->info('Found ' . $testPatients->count() . ' test patient(s):');
        $this->newLine();

        // Display patients that will be deleted
        $headers = ['Patient ID', 'Name', 'Age', 'Contact', 'Created'];
        $rows = [];

        foreach ($testPatients as $patient) {
            $consultations = Consultation::where('patient_id', $patient->id)->count();
            $labResults = LabResult::where('patient_id', $patient->id)->count();
            
            $rows[] = [
                $patient->patient_code,
                $patient->first_name . ' ' . $patient->last_name,
                $patient->age . ' years',
                $patient->phone_number ?? 'N/A',
                $patient->created_at->format('Y-m-d'),
            ];
            
            if ($consultations > 0 || $labResults > 0) {
                $this->line("  └─ Has {$consultations} consultation(s) and {$labResults} lab result(s)");
            }
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Count related records
        $totalConsultations = 0;
        $totalLabResults = 0;
        foreach ($testPatients as $patient) {
            $totalConsultations += Consultation::where('patient_id', $patient->id)->count();
            $totalLabResults += LabResult::where('patient_id', $patient->id)->count();
        }

        if ($totalConsultations > 0 || $totalLabResults > 0) {
            $this->warn("⚠️  This will also delete:");
            $this->warn("   - {$totalConsultations} consultation record(s)");
            $this->warn("   - {$totalLabResults} lab result record(s)");
            $this->newLine();
        }

        if ($this->option('dry-run')) {
            $this->warn('🔸 DRY RUN MODE - No records were deleted');
            $this->info('Run without --dry-run to actually delete these records');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete these ' . $testPatients->count() . ' patient(s) and all related records?', false)) {
                $this->info('❌ Operation cancelled');
                return 0;
            }
        }

        // Delete patients and related records
        $deleted = 0;
        foreach ($testPatients as $patient) {
            try {
                $patientName = $patient->first_name . ' ' . $patient->last_name;
                
                // Delete related records first
                Consultation::where('patient_id', $patient->id)->delete();
                LabResult::where('patient_id', $patient->id)->delete();
                
                // Delete patient
                $patient->delete();
                
                $deleted++;
                $this->line("  ✓ Deleted: {$patientName}");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to delete: {$patient->first_name} {$patient->last_name} - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("✅ Successfully deleted {$deleted} patient record(s) and all related data!");
        
        return 0;
    }
}

