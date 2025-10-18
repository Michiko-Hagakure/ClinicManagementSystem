<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowOwnerAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:show-owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display owner account details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $owner = User::where('role', 'owner')->first();

        if (!$owner) {
            $this->error('❌ No owner account found!');
            $this->info('Please create an owner account via the Admin Panel.');
            return 1;
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('👔 OWNER ACCOUNT DETAILS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->info('📋 Name: ' . $owner->name);
        $this->info('🔑 Employee ID: ' . ($owner->employee_id ?? 'N/A'));
        $this->info('📧 Email: ' . $owner->email);
        $this->info('👤 Role: ' . $owner->getRoleDisplayName());
        $this->info('🏢 Department: ' . ($owner->department ?? 'N/A'));
        $this->info('✅ Status: ' . ($owner->is_active ? 'Active' : 'Inactive'));
        $this->line('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🔐 LOGIN INSTRUCTIONS:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->info('1. Go to: http://127.0.0.1:8000/login');
        $this->info('2. Enter Employee ID: ' . ($owner->employee_id ?? 'N/A'));
        $this->info('3. Enter Password: (set by admin)');
        $this->info('4. You will be redirected to Owner Dashboard');
        $this->line('');
        $this->warn('⚠️  If you don\'t know the password, an admin can reset it.');
        $this->line('');

        return 0;
    }
}

