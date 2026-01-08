<?php

use App\User;
use App\Student;
use App\Parents;
use App\Grade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 10 dummy students with parents for each class.
     */
    public function run()
    {
        $classes = Grade::all();
        
        if ($classes->isEmpty()) {
            $this->command->info('No classes found. Please create classes first.');
            return;
        }

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'James', 'Emma', 'Daniel', 'Olivia', 
                       'William', 'Sophia', 'Alexander', 'Isabella', 'Benjamin', 'Mia', 'Lucas', 'Charlotte', 'Henry', 'Amelia',
                       'Tendai', 'Tatenda', 'Tinashe', 'Tafadzwa', 'Tapiwa', 'Tariro', 'Tawanda', 'Tinotenda', 'Takunda', 'Tanaka',
                       'Chiedza', 'Chenai', 'Chipo', 'Rufaro', 'Rudo', 'Nyasha', 'Anesu', 'Farai', 'Kudakwashe', 'Munashe'];
        
        $lastNames = ['Moyo', 'Ncube', 'Dube', 'Ndlovu', 'Sibanda', 'Mpofu', 'Nkomo', 'Nyoni', 'Chikwanda', 'Banda',
                      'Zimuto', 'Mutasa', 'Chimedza', 'Mapfumo', 'Mudzingwa', 'Chigumba', 'Mazarura', 'Chikowore', 'Mukwashi', 'Gumbo',
                      'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Wilson', 'Moore', 'Taylor'];

        $genders = ['Male', 'Female'];
        $studentTypes = ['day', 'boarding'];
        
        $studentCount = 0;
        $parentCount = 0;

        foreach ($classes as $class) {
            $this->command->info("Creating students for class: {$class->class_name}");
            
            // Get the highest roll number for this class
            $maxRoll = Student::where('class_id', $class->id)->max('roll_number') ?? 0;
            
            for ($i = 1; $i <= 10; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $gender = $genders[array_rand($genders)];
                $studentType = $studentTypes[array_rand($studentTypes)];
                
                // Create parent user
                $parentFirstName = $firstNames[array_rand($firstNames)];
                $parentEmail = strtolower($parentFirstName . '.' . $lastName . '.' . Str::random(4) . '@parent.test');
                
                $parentUser = User::create([
                    'name' => $parentFirstName . ' ' . $lastName,
                    'email' => $parentEmail,
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                    'must_change_password' => true,
                ]);
                $parentUser->assignRole('Parent');
                
                // Create parent record
                $parent = Parents::create([
                    'user_id' => $parentUser->id,
                    'gender' => $genders[array_rand($genders)],
                    'phone' => '07' . rand(10000000, 99999999),
                    'current_address' => rand(1, 999) . ' Sample Street, Harare',
                    'permanent_address' => rand(1, 999) . ' Home Address, Zimbabwe',
                    'registration_completed' => true,
                ]);
                $parentCount++;
                
                // Create student user
                $studentEmail = strtolower($firstName . '.' . $lastName . '.' . Str::random(4) . '@student.test');
                
                $studentUser = User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $studentEmail,
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                    'must_change_password' => true,
                ]);
                $studentUser->assignRole('Student');
                
                // Create student record
                $maxRoll++;
                $student = Student::create([
                    'user_id' => $studentUser->id,
                    'parent_id' => $parent->id,
                    'class_id' => $class->id,
                    'roll_number' => $maxRoll,
                    'gender' => $gender,
                    'phone' => '07' . rand(10000000, 99999999),
                    'dateofbirth' => now()->subYears(rand(12, 18))->subDays(rand(1, 365))->format('Y-m-d'),
                    'current_address' => rand(1, 999) . ' Student Street, Harare',
                    'permanent_address' => rand(1, 999) . ' Home Address, Zimbabwe',
                    'is_transferred' => false,
                    'student_type' => $studentType,
                ]);
                
                // Link student to parent via pivot table
                $student->parents()->attach($parent->id);
                
                $studentCount++;
            }
        }

        $this->command->info("Successfully created {$studentCount} students and {$parentCount} parents across " . $classes->count() . " classes.");
    }
}
