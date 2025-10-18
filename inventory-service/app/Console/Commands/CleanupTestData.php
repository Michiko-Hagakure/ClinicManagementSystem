<?php

namespace App\Console\Commands;

use App\Models\DispenseMedicine;
use Illuminate\Console\Command;

class CleanupTestData extends Command
{
    protected $signature = 'inventory:cleanup-test {--force : Skip confirmation}';
    protected $description = 'Remove all test dispensation records';

    public function handle()
    {
        $this->warn('⚠️  This will delete ALL dispensation records (medicine sales history)!');
        $this->info('Note: Medicine inventory itself will NOT be deleted, only sales records.');
        $this->newLine();

        $totalDispensations = DispenseMedicine::count();

        $this->info("Found:");
        $this->line("  - {$totalDispensations} dispensation record(s)");
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all dispensation records?', false)) {
                $this->info('❌ Operation cancelled');
                return 0;
            }
        }

        try {
            DispenseMedicine::truncate();

            $this->info('✅ Successfully deleted all dispensation records!');
            $this->info('Medicine inventory remains intact.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}

