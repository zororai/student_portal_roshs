<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeRemainingToOnlineExerciseSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_exercise_submissions', function (Blueprint $table) {
            $table->integer('time_remaining_seconds')->nullable()->after('started_at');
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
            $table->dropColumn('time_remaining_seconds');
        });
    }
}
