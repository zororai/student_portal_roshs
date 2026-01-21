<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Parents;
use Illuminate\Support\Facades\Hash;

class CreateDummyParent extends Command
{
    protected $signature = 'make:dummy-parent {--link-student= : Student ID to link with the parent}';

    protected $description = 'Create a dummy parent account for testing';

    public function handle()
    {
        $studentId = $this->option('link-student');
        
        // Check if parent already exists
        $existing = User::where('email', 'parent@test.com')->first();
        if ($existing) {
            // If parent exists but we want to link a student
            if ($studentId) {
                $parent = Parents::where('user_id', $existing->id)->first();
                if ($parent) {
                    $student = \App\Student::find($studentId);
                    if ($student) {
                        $parent->students()->syncWithoutDetaching([$studentId]);
                        $this->info("✅ Linked existing parent with student: {$student->user->name} (ID: {$studentId})");
                        return 0;
                    } else {
                        $this->error("Student with ID {$studentId} not found!");
                        return 1;
                    }
                }
            }
            $this->error('Parent with email parent@test.com already exists!');
            return 1;
        }

        // Create user for parent
        $user = User::create([
            'name' => 'Test Parent',
            'email' => 'parent@test.com',
            'phone' => '0771234567',
            'password' => Hash::make('12345678'),
            'is_active' => true,
            'must_change_password' => false,
        ]);

        // Assign parent role
        $user->assignRole('Parent');

        // Create parent record
        Parents::create([
            'user_id' => $user->id,
            'gender' => 'male',
            'phone' => '0771234567',
            'current_address' => '123 Test Street, Harare',
            'permanent_address' => '123 Test Street, Harare',
            'registration_completed' => true,
        ]);

        $this->info('');
        $this->info('✅ Dummy parent created successfully!');
        $this->info('');
        $this->table(['Field', 'Value'], [
            ['Email', 'parent@test.com'],
            ['Password', '12345678'],
            ['Phone', '0771234567'],
            ['Name', 'Test Parent'],
        ]);

        return 0;
    }
}
