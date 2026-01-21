<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use App\Parents;
use Illuminate\Support\Facades\Hash;

class DummyParentSeeder extends Seeder
{
    public function run()
    {
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

        $this->command->info('Dummy parent created successfully!');
        $this->command->info('Email: parent@test.com');
        $this->command->info('Password: 12345678');
    }
}
