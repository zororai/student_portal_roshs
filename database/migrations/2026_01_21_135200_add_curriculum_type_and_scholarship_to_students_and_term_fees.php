<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurriculumTypeAndScholarshipToStudentsAndTermFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add curriculum_type and scholarship_percentage to students table
        Schema::table('students', function (Blueprint $table) {
            $table->enum('curriculum_type', ['zimsec', 'cambridge'])->default('zimsec')->after('student_type');
            $table->decimal('scholarship_percentage', 5, 2)->default(0)->after('curriculum_type');
        });

        // Add curriculum_type to term_fees table
        Schema::table('term_fees', function (Blueprint $table) {
            $table->enum('curriculum_type', ['zimsec', 'cambridge'])->default('zimsec')->after('student_type');
        });

        // Add curriculum-specific totals to results_statuses table
        Schema::table('results_statuses', function (Blueprint $table) {
            $table->decimal('zimsec_day_fees', 10, 2)->default(0)->after('total_boarding_fees');
            $table->decimal('zimsec_boarding_fees', 10, 2)->default(0)->after('zimsec_day_fees');
            $table->decimal('cambridge_day_fees', 10, 2)->default(0)->after('zimsec_boarding_fees');
            $table->decimal('cambridge_boarding_fees', 10, 2)->default(0)->after('cambridge_day_fees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['curriculum_type', 'scholarship_percentage']);
        });

        Schema::table('term_fees', function (Blueprint $table) {
            $table->dropColumn('curriculum_type');
        });

        Schema::table('results_statuses', function (Blueprint $table) {
            $table->dropColumn(['zimsec_day_fees', 'zimsec_boarding_fees', 'cambridge_day_fees', 'cambridge_boarding_fees']);
        });
    }
}
