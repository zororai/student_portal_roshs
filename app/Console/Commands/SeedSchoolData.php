<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Teacher;
use App\Grade;
use App\Student;
use App\Subject;
use App\Parents;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SeedSchoolData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:seed {--teachers=5} {--classes=3} {--students=30} {--subjects=6} {--parents=15}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with teachers, classes, students, and subjects';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting school data seeding...');
        
        $teacherCount = $this->option('teachers');
        $classCount = $this->option('classes');
        $studentCount = $this->option('students');
        $subjectCount = $this->option('subjects');
        $parentCount = $this->option('parents');

        // Ensure roles exist
        $this->ensureRolesExist();

        // Create teachers first (subjects need teacher_id)
        $teachers = $this->createTeachers($teacherCount);
        $this->info("✓ Created {$teacherCount} teachers");

        // Create subjects and assign teachers
        $subjects = $this->createSubjects($subjectCount, $teachers);
        $this->info("✓ Created {$subjectCount} subjects");

        // Create classes and assign subjects
        $classes = $this->createClasses($classCount, $teachers, $subjects);
        $this->info("✓ Created {$classCount} classes");

        // Create parents
        $parents = $this->createParents($parentCount);
        $this->info("✓ Created {$parentCount} parents");

        // Create students and assign to classes and parents
        $this->createStudents($studentCount, $classes, $parents);
        $this->info("✓ Created {$studentCount} students");

        $this->info('✓ School data seeding completed successfully!');
        return 0;
    }

    private function ensureRolesExist()
    {
        $roles = ['Admin', 'Teacher', 'Parent', 'Student'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
    }

    private function createSubjects($count, $teachers)
    {
        $subjectNames = [
            'Mathematics', 'English', 'Science', 'History', 'Geography',
            'Physics', 'Chemistry', 'Biology', 'Computer Science', 'Art',
            'Music', 'Physical Education', 'Economics', 'Business Studies'
        ];

        $subjects = [];
        for ($i = 0; $i < min($count, count($subjectNames)); $i++) {
            $teacher = $teachers[$i % count($teachers)];
            
            // Generate subject code: F + first 2 letters of subject name
            $firstTwoLetters = strtoupper(substr($subjectNames[$i], 0, 2));
            $subjectCode = 'F' . $firstTwoLetters;
            
            $subject = Subject::firstOrCreate(
                ['name' => $subjectNames[$i]],
                [
                    'slug' => strtolower(str_replace(' ', '-', $subjectNames[$i])),
                    'subject_code' => $subjectCode,
                    'teacher_id' => $teacher->id,
                    'description' => 'Description for ' . $subjectNames[$i]
                ]
            );
            $subjects[] = $subject;
        }

        return $subjects;
    }

    private function createTeachers($count)
    {
        $teachers = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $user = User::firstOrCreate(
                ['email' => "teacher{$i}@school.com"],
                [
                    'name' => "Teacher {$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true
                ]
            );

            $user->syncRoles(['Teacher']);

            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => '07' . str_pad($i, 8, '0', STR_PAD_LEFT),
                    'dateofbirth' => '1985-01-01',
                    'gender' => $i % 2 == 0 ? 'Female' : 'Male',
                    'current_address' => "Address {$i}, City",
                    'permanent_address' => "Address {$i}, City"
                ]
            );

            $teachers[] = $teacher;
        }

        return $teachers;
    }

    private function createClasses($count, $teachers, $subjects)
    {
        $classNames = [
            'Form 1A', 'Form 1B', 'Form 2A', 'Form 2B', 'Form 3A', 'Form 3B',
            'Form 4A', 'Form 4B', 'Lower Sixth', 'Upper Sixth'
        ];

        $classes = [];

        for ($i = 0; $i < min($count, count($classNames)); $i++) {
            $teacher = $teachers[$i % count($teachers)];

            $class = Grade::firstOrCreate(
                ['class_name' => $classNames[$i]],
                [
                    'teacher_id' => $teacher->id,
                    'class_numeric' => $i + 1,
                    'class_description' => 'Description for ' . $classNames[$i]
                ]
            );

            // Assign subjects to class
            $subjectsToAssign = array_slice($subjects, 0, min(6, count($subjects)));
            foreach ($subjectsToAssign as $subject) {
                $class->subjects()->syncWithoutDetaching($subject->id);
            }

            $classes[] = $class;
        }

        return $classes;
    }

    private function createParents($count)
    {
        $parents = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $user = User::firstOrCreate(
                ['email' => "parent{$i}@school.com"],
                [
                    'name' => "Parent {$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true
                ]
            );

            $user->syncRoles(['Parent']);

            $parent = Parents::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => '07' . str_pad($i + 2000, 8, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 == 0 ? 'Female' : 'Male',
                    'current_address' => "Parent Address {$i}",
                    'permanent_address' => "Parent Address {$i}"
                ]
            );

            $parents[] = $parent;
        }

        return $parents;
    }

    private function createStudents($count, $classes, $parents)
    {
        for ($i = 1; $i <= $count; $i++) {
            $user = User::firstOrCreate(
                ['email' => "student{$i}@school.com"],
                [
                    'name' => "Student {$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true
                ]
            );

            $user->syncRoles(['Student']);

            $class = $classes[$i % count($classes)];
            $parent = $parents[$i % count($parents)];

            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => "Student {$i}",
                    'email' => "student{$i}@school.com",
                    'roll_number' => str_pad($i, 4, '0', STR_PAD_LEFT),
                    'phone' => '07' . str_pad($i + 1000, 8, '0', STR_PAD_LEFT),
                    'dateofbirth' => '2005-01-01',
                    'gender' => $i % 2 == 0 ? 'Female' : 'Male',
                    'current_address' => "Student Address {$i}",
                    'permanent_address' => "Student Address {$i}",
                    'class_id' => $class->id,
                    'parent_id' => $parent->id
                ]
            );

            // Link student to parent via pivot table
            $student->parents()->syncWithoutDetaching([$parent->id]);
        }
    }
}
