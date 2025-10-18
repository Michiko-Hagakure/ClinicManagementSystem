<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LabResult;

class FixLabResultTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lab:fix-timestamps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix lab result timestamps to use actual creation time instead of midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing lab result timestamps...');
        
        // Find lab results with midnight timestamps (00:00:00)
        $labResults = LabResult::whereTime('test_date', '=', '00:00:00')->get();
        
        $count = $labResults->count();
        $this->info("Found {$count} lab results with incorrect timestamps");
        
        if ($count === 0) {
            $this->info('No lab results need fixing!');
            return 0;
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($labResults as $labResult) {
            // Update test_date to use created_at timestamp
            $labResult->update([
                'test_date' => $labResult->created_at
            ]);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated {$count} lab result timestamps!");
        
        return 0;
    }
}
