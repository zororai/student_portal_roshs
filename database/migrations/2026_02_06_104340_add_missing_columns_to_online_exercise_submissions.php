<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToOnlineExerciseSubmissions extends Migration
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
        
        Schema::table('online_exercise_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exercise_submissions', 'status')) {
                $table->string('status')->default('not_started')->after('student_id');
            }
            if (!Schema::hasColumn('online_exercise_submissions', 'total_score')) {
                $table->decimal('total_score', 8, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('online_exercise_submissions', 'teacher_feedback')) {
                $table->text('teacher_feedback')->nullable()->after('feedback');
            }
            if (!Schema::hasColumn('online_exercise_submissions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('teacher_feedback');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_exercise_submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'total_score', 'teacher_feedback', 'started_at']);
        });
    }
}
