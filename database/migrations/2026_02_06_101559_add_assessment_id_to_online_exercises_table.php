<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssessmentIdToOnlineExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if online_exercises table doesn't exist (exercises table is used instead)
        if (!Schema::hasTable('online_exercises')) {
            return;
        }
        
        Schema::table('online_exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('assessment_id')->nullable()->after('id');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online_exercises', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropColumn('assessment_id');
        });
    }
}
