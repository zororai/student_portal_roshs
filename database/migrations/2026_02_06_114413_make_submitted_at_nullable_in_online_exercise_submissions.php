<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSubmittedAtNullableInOnlineExerciseSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if online_exercise_submissions table doesn't exist (exercise_submissions table is used instead)
        if (!Schema::hasTable('online_exercise_submissions')) {
            return;
        }
        
        \DB::statement('ALTER TABLE `online_exercise_submissions` MODIFY `submitted_at` TIMESTAMP NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE `online_exercise_submissions` MODIFY `submitted_at` TIMESTAMP NOT NULL');
    }
}
