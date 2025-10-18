<?php

namespace App\Console\Commands;

use App\Models\MedicalBill;
use App\Models\BillItem;
use App\Models\Patient;
use Illuminate\Console\Command;

class CleanupTestData extends Command
{
    protected $signature = 'pos:cleanup-test {--force : Skip confirmation}';
    protected $description = 'Remove all test billing data from POS';

    public function handle()
    {
        $this->warn('⚠️  This will delete ALL billing data in the POS system!');
        $this->newLine();

        $totalBills = MedicalBill::count();
        $totalItems = BillItem::count();
        $totalPatients = Patient::count();

        $this->info("Found:");
        $this->line("  - {$totalBills} medical bill(s)");
        $this->line("  - {$totalItems} bill item(s)");
        $this->line("  - {$totalPatients} patient(s)");
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all billing data?', false)) {
                $this->info('❌ Operation cancelled');
                return 0;
            }
        }

        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            BillItem::truncate();
            MedicalBill::truncate();
            Patient::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('✅ Successfully deleted all POS data!');
            return 0;
        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}

