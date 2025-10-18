<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use App\Models\DispenseMedicine;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateSampleDispenseRecords extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dispense:create-samples {--count=20 : Number of sample records to create}';

    /**
     * The console command description.
     */
    protected $description = 'Create sample dispense medicine records for testing reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info("Creating {$count} sample dispense records...");
        
        // Get random medicines
        $medicines = Medicine::where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->limit(min($count, 20))
            ->get();
        
        if ($medicines->isEmpty()) {
            $this->error('No medicines found in inventory!');
            return 1;
        }
        
        $created = 0;
        $dateRangeStart = now()->subDays(30);
        $dateRangeEnd = now();
        
        foreach ($medicines as $medicine) {
            // Create 1-3 dispense records per medicine
            $recordsPerMedicine = rand(1, 3);
            
            for ($i = 0; $i < $recordsPerMedicine; $i++) {
                $randomDate = Carbon::parse($dateRangeStart)->addDays(rand(0, 30));
                $quantity = rand(1, min($medicine->stock_quantity, 10));
                
                DispenseMedicine::create([
                    'medicine_id' => $medicine->medicine_id,
                    'patient_id' => rand(1, 5), // Random patient ID
                    'quantity' => $quantity,
                    'date' => $randomDate
                ]);
                
                $created++;
                
                if ($created >= $count) {
                    break 2;
                }
            }
        }
        
        $this->info("✓ Successfully created {$created} sample dispense records!");
        $this->info('You can now view the Top Selling Medicines report in the Owner Dashboard.');
        
        // Show some stats
        $totalDispensed = DispenseMedicine::count();
        $topMedicine = DispenseMedicine::select('medicine_id')
            ->selectRaw('SUM(quantity) as total')
            ->groupBy('medicine_id')
            ->orderByDesc('total')
            ->with('medicine')
            ->first();
        
        if ($topMedicine) {
            $this->line('');
            $this->line("📊 Current Stats:");
            $this->line("   Total Dispensations: {$totalDispensed}");
            $this->line("   Top Selling: {$topMedicine->medicine->name} ({$topMedicine->total} units)");
        }
        
        return 0;
    }
}
