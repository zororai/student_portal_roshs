<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parents;
use App\User;
use Illuminate\Support\Facades\DB;

class CleanParentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parents:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all parents and their associated data from the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $parentCount = Parents::count();
        
        if ($parentCount === 0) {
            $this->info('No parents found in the database.');
            return 0;
        }

        $this->warn("⚠️  WARNING: This will delete ALL {$parentCount} parents and their associated data!");
        $this->warn('This action CANNOT be undone.');
        
        if (!$this->confirm('Are you absolutely sure you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Double confirmation
        if (!$this->confirm('This is your last chance. Delete ALL parents?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Starting deletion process...');
        
        DB::beginTransaction();
        
        try {
            // Get all parent user IDs before deletion
            $parentUserIds = Parents::with('user')->get()->pluck('user.id')->filter();
            
            $this->info('Deleting parent records...');
            $deletedParents = Parents::count();
            Parents::query()->delete();
            
            $this->info("✓ Deleted {$deletedParents} parent records");
            
            // Delete associated user accounts
            if ($parentUserIds->isNotEmpty()) {
                $this->info('Deleting associated user accounts...');
                $deletedUsers = User::whereIn('id', $parentUserIds)->delete();
                $this->info("✓ Deleted {$deletedUsers} user accounts");
            }
            
            DB::commit();
            
            $this->info('');
            $this->info('✅ Database cleaned successfully!');
            $this->info("Total parents deleted: {$deletedParents}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred during deletion: ' . $e->getMessage());
            return 1;
        }
    }
}
