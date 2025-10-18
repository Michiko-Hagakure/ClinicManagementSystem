<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:cleanup-test {--dry-run : Show what would be deleted without actually deleting} {--force : Skip confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Remove test/dummy user accounts from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning for test/dummy user accounts (without profile pictures)...');
        $this->newLine();

        // List of test users to remove (based on common test names)
        $testNames = [
            'Sarah Johnson',
            'James Wilson',
            'Lisa Chen',
            'Mary Angels',
            'Anna Rodriguez',
            'Michael Brown',
            'Dr. Maria Santos',
            'Doctor',
            'Clinic Staff',
            'Cashier',
        ];

        // Find test users WITHOUT profile pictures (except System Administrator)
        $testUsers = User::where(function($query) use ($testNames) {
                $query->whereIn('name', $testNames)
                    ->orWhere('name', 'LIKE', 'Test%')
                    ->orWhere('name', 'LIKE', 'Demo%')
                    ->orWhere('name', 'LIKE', 'Dummy%');
            })
            ->where(function($query) {
                $query->whereNull('profile_picture')
                    ->orWhere('profile_picture', '');
            })
            ->where('role', '!=', 'admin') // Don't delete admin accounts
            ->get();

        if ($testUsers->isEmpty()) {
            $this->info('✅ No test users found!');
            return 0;
        }

        $this->info('Found ' . $testUsers->count() . ' test user(s):');
        $this->newLine();

        // Display users that will be deleted
        $headers = ['ID', 'Name', 'Employee ID', 'Role', 'Created'];
        $rows = [];

        foreach ($testUsers as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->employee_id,
                $user->getRoleDisplayName(),
                $user->created_at->format('Y-m-d'),
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔸 DRY RUN MODE - No users were deleted');
            $this->info('Run without --dry-run to actually delete these users');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete these ' . $testUsers->count() . ' user(s)?', false)) {
                $this->info('❌ Operation cancelled');
                return 0;
            }
        }

        // Delete users
        $deleted = 0;
        foreach ($testUsers as $user) {
            try {
                $userName = $user->name;
                $user->delete();
                $deleted++;
                $this->line("  ✓ Deleted: {$userName}");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to delete: {$user->name} - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("✅ Successfully deleted {$deleted} test user(s)!");
        
        return 0;
    }
}

