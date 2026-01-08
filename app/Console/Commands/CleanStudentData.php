<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Student;
use App\User;
use Illuminate\Support\Facades\DB;

class CleanStudentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all students and their associated data from the database';

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
        $studentCount = Student::count();
        
        if ($studentCount === 0) {
            $this->info('No students found in the database.');
            return 0;
        }

        $this->warn("⚠️  WARNING: This will delete ALL {$studentCount} students and their associated data!");
        $this->warn('This action CANNOT be undone.');
        
        if (!$this->confirm('Are you absolutely sure you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Double confirmation
        if (!$this->confirm('This is your last chance. Delete ALL students?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Starting deletion process...');
        
        DB::beginTransaction();
        
        try {
            // Get all student user IDs before deletion
            $studentUserIds = Student::with('user')->get()->pluck('user.id')->filter();
            
            $this->info('Deleting student records...');
            $deletedStudents = Student::count();
            Student::query()->delete();
            
            $this->info("✓ Deleted {$deletedStudents} student records");
            
            // Delete associated user accounts
            if ($studentUserIds->isNotEmpty()) {
                $this->info('Deleting associated user accounts...');
                $deletedUsers = User::whereIn('id', $studentUserIds)->delete();
                $this->info("✓ Deleted {$deletedUsers} user accounts");
            }
            
            DB::commit();
            
            $this->info('');
            $this->info('✅ Database cleaned successfully!');
            $this->info("Total students deleted: {$deletedStudents}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred during deletion: ' . $e->getMessage());
            return 1;
        }
    }
}
