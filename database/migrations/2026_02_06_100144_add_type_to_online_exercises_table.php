<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToOnlineExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('online_exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('online_exercises', 'type')) {
                $table->string('type')->default('quiz')->after('instructions');
            }
            if (!Schema::hasColumn('online_exercises', 'total_marks')) {
                $table->integer('total_marks')->default(0)->after('type');
            }
            if (!Schema::hasColumn('online_exercises', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('total_marks');
            }
            if (!Schema::hasColumn('online_exercises', 'due_date')) {
                $table->datetime('due_date')->nullable()->after('duration_minutes');
            }
            if (!Schema::hasColumn('online_exercises', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('due_date');
            }
            if (!Schema::hasColumn('online_exercises', 'show_results')) {
                $table->boolean('show_results')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('online_exercises', 'term')) {
                $table->string('term')->nullable()->after('show_results');
            }
            if (!Schema::hasColumn('online_exercises', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('term');
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
        Schema::table('online_exercises', function (Blueprint $table) {
            //
        });
    }
}
