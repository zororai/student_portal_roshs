<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Grade;
use App\Student;
use App\Parents;
use App\User;

class TransferOrGenerateStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:transfer
                            {source=Form 5 : Source class name}
                            {dest=Form 6 : Destination class name}
                            {--count=10 : Number of students to process}
                            {--generate : Create new random students in destination}
                            {--promote : Promote existing students from source to destination}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote or generate random students (and parents) from one class to another';

    public function handle()
    {
        $sourceName = $this->argument('source');
        $destName = $this->argument('dest');
        $count = (int)$this->option('count');
        $generate = $this->option('generate');
        $promote = $this->option('promote');

        if (! $generate && ! $promote) {
            // default to generate
            $generate = true;
        }

        $source = Grade::where('class_name', $sourceName)->first();
        $dest = Grade::where('class_name', $destName)->first();

        if (! $dest) {
            $this->error("Destination class '$destName' not found.");
            return 1;
        }

        if ($promote) {
            if (! $source) {
                $this->error("Source class '$sourceName' not found.");
                return 1;
            }

            $total = Student::where('class_id', $source->id)->count();
            if ($total === 0) {
                $this->info('No students found to promote.');
                return 0;
            }

            $take = min($count, $total);
            $students = Student::where('class_id', $source->id)->inRandomOrder()->take($take)->get();

            DB::transaction(function () use ($students, $dest) {
                foreach ($students as $student) {
                    // create parent if none
                    if (! $student->parent_id) {
                        $parentUser = User::create([
                            'name' => $student->user ? ($student->user->name.' Parent') : 'Parent of '.$student->id,
                            'email' => 'parent_'.uniqid().'@example.test',
                            'password' => bcrypt('password'),
                        ]);
                        $parentUser->assignRole('Parent');

                        $parent = Parents::create([
                            'user_id' => $parentUser->id,
                            'gender' => 'male',
                            'phone' => null,
                            'current_address' => $student->current_address,
                            'permanent_address' => $student->permanent_address,
                            'created_at' => now(),
                        ]);

                        $student->parent_id = $parent->id;
                    }

                    $student->class_id = $dest->id;
                    $student->save();
                }
            });

            $this->info("Promoted {$students->count()} students from {$sourceName} to {$destName}.");
            return 0;
        }

        if ($generate) {
            $faker = Faker::create();

            DB::transaction(function () use ($count, $dest, $faker) {
                for ($i = 0; $i < $count; $i++) {
                    $studentUser = User::create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'password' => bcrypt('password'),
                    ]);
                    $studentUser->assignRole('Student');

                    $parentUser = User::create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'password' => bcrypt('password'),
                    ]);
                    $parentUser->assignRole('Parent');

                    $parent = Parents::create([
                        'user_id' => $parentUser->id,
                        'gender' => $faker->randomElement(['male','female']),
                        'phone' => $faker->phoneNumber,
                        'current_address' => $faker->address,
                        'permanent_address' => $faker->address,
                        'created_at' => now(),
                    ]);

                    // determine next roll number for this class (padded)
                    $maxRoll = Student::where('class_id', $dest->id)->max('roll_number');
                    $nextRoll = $maxRoll ? ((int) $maxRoll + 1) : 1;
                    $rollNumber = str_pad($nextRoll, 4, '0', STR_PAD_LEFT);

                    $student = Student::create([
                        'user_id' => $studentUser->id,
                        'parent_id' => $parent->id,
                        'class_id' => $dest->id,
                        'roll_number' => $rollNumber,
                        'gender' => $faker->randomElement(['male','female']),
                        'phone' => $faker->phoneNumber,
                        'dateofbirth' => $faker->date('Y-m-d', '-16 years'),
                        'current_address' => $faker->address,
                        'permanent_address' => $faker->address,
                        'created_at' => now(),
                    ]);
                }
            });

            $this->info("Created {$count} random students (and parents) in {$destName}.");
            return 0;
        }

        $this->info('Nothing to do.');
        return 0;
    }
}
