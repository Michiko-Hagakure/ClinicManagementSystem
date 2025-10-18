<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicalBill;
use App\Models\BillItem;
use Carbon\Carbon;

class FixTimezones extends Command
{
    protected $signature = 'timezone:fix';
    protected $description = 'Convert UTC timestamps to Manila timezone (UTC+8) for existing records';

    public function handle()
    {
        $this->info('Converting timestamps from UTC to Manila time (adding 8 hours)...');

        // Fix medical bills
        $bills = MedicalBill::all();
        $billCount = 0;
        
        foreach ($bills as $bill) {
            $updated = false;
            
            // Add 8 hours to convert UTC to Manila time
            if ($bill->bill_date) {
                $bill->bill_date = Carbon::parse($bill->bill_date)->addHours(8);
                $updated = true;
            }
            
            if ($bill->created_at) {
                $bill->created_at = Carbon::parse($bill->created_at)->addHours(8);
                $updated = true;
            }
            
            if ($bill->updated_at) {
                $bill->updated_at = Carbon::parse($bill->updated_at)->addHours(8);
                $updated = true;
            }
            
            if ($bill->paid_at) {
                $bill->paid_at = Carbon::parse($bill->paid_at)->addHours(8);
                $updated = true;
            }
            
            if ($updated) {
                // Use timestamps(false) to prevent auto-updating updated_at again
                $bill->timestamps = false;
                $bill->save();
                $billCount++;
                $this->line("  Updated Bill #{$bill->id}: {$bill->bill_number}");
            }
        }

        // Fix bill items
        $items = BillItem::all();
        $itemCount = 0;
        
        foreach ($items as $item) {
            $updated = false;
            
            // Add 8 hours to convert UTC to Manila time
            if ($item->service_date) {
                $item->service_date = Carbon::parse($item->service_date)->addHours(8);
                $updated = true;
            }
            
            if ($item->created_at) {
                $item->created_at = Carbon::parse($item->created_at)->addHours(8);
                $updated = true;
            }
            
            if ($item->updated_at) {
                $item->updated_at = Carbon::parse($item->updated_at)->addHours(8);
                $updated = true;
            }
            
            if ($updated) {
                $item->timestamps = false;
                $item->save();
                $itemCount++;
            }
        }

        $this->newLine();
        $this->info("✅ Updated {$billCount} medical bills");
        $this->info("✅ Updated {$itemCount} bill items");
        $this->info('All timestamps converted to Manila timezone!');

        return 0;
    }
}

