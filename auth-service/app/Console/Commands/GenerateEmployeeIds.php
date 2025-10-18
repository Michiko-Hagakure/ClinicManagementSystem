<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateEmployeeIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-employee-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique 6-digit employee IDs for users who dont have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Employee IDs for users...');
        
        $usersWithoutEmployeeId = User::whereNull('employee_id')->get();
        
        if ($usersWithoutEmployeeId->isEmpty()) {
            $this->info('All users already have Employee IDs.');
            return 0;
        }
        
        $count = 0;
        foreach ($usersWithoutEmployeeId as $user) {
            $user->employee_id = User::generateEmployeeId();
            $user->save();
            
            $this->line("✓ Generated Employee ID {$user->employee_id} for {$user->name}");
            $count++;
        }
        
        $this->info("\n✓ Successfully generated {$count} Employee IDs!");
        
        return 0;
    }
}
