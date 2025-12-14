<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Student;
use App\Parents;
use App\Grade;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get first available class
        $class = Grade::first();
        
        // Create test parent user FIRST
        $parentUser = User::updateOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Test Parent',
                'password' => Hash::make('password123'),
                'is_active' => 1,
            ]
        );
        $parentUser->assignRole('Parent');

        // Create parent record
        $parent = Parents::updateOrCreate(
            ['user_id' => $parentUser->id],
            [
                'gender' => 'male',
                'phone' => '0779876543',
                'current_address' => 'Parent Test Address',
                'permanent_address' => 'Parent Test Address',
            ]
        );

        // Create test student user
        $studentUser = User::updateOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password123'),
                'is_active' => 1,
            ]
        );
        $studentUser->assignRole('Student');

        // Create student record with parent_id
        Student::updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'parent_id' => $parent->id,
                'class_id' => $class ? $class->id : null,
                'roll_number' => 1001,
                'gender' => 'male',
                'phone' => '0771234567',
                'dateofbirth' => '2008-01-15',
                'current_address' => 'Test Address',
                'permanent_address' => 'Test Address',
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Student: student@test.com / password123');
        $this->command->info('Parent: parent@test.com / password123');
    }
}
