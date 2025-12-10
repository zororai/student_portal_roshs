<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeRollNumberToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, alter the column type to varchar
        DB::statement('ALTER TABLE students MODIFY roll_number VARCHAR(20) NOT NULL');

        // Then, update all existing roll numbers to string format
        $students = DB::table('students')->get();
        foreach ($students as $student) {
            $newRollNumber = 'RSH' . str_pad($student->roll_number, 4, '0', STR_PAD_LEFT);
            DB::table('students')
                ->where('id', $student->id)
                ->update(['roll_number' => $newRollNumber]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Extract numeric part and convert back
        $students = DB::table('students')->get();
        foreach ($students as $student) {
            $numericPart = (int) substr($student->roll_number, 3);
            DB::table('students')
                ->where('id', $student->id)
                ->update(['roll_number' => $numericPart]);
        }

        DB::statement('ALTER TABLE students MODIFY roll_number BIGINT UNSIGNED NOT NULL');
    }
}
