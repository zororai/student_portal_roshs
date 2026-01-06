<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLessonTypesToSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedInteger('single_lessons_per_week')->default(0)->after('teacher_id');
            $table->unsignedInteger('double_lessons_per_week')->default(0)->after('single_lessons_per_week');
            $table->unsignedInteger('triple_lessons_per_week')->default(0)->after('double_lessons_per_week');
            $table->unsignedInteger('quad_lessons_per_week')->default(0)->after('triple_lessons_per_week');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['single_lessons_per_week', 'double_lessons_per_week', 'triple_lessons_per_week', 'quad_lessons_per_week']);
        });
    }
}
