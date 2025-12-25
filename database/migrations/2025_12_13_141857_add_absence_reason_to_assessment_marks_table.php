<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbsenceReasonToAssessmentMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('assessment_marks', 'absence_reason')) {
            Schema::table('assessment_marks', function (Blueprint $table) {
                $table->string('absence_reason')->nullable()->after('comment');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('assessment_marks', 'absence_reason')) {
            Schema::table('assessment_marks', function (Blueprint $table) {
                $table->dropColumn('absence_reason');
            });
        }
    }
}
