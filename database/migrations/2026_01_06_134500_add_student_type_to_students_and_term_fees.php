<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentTypeToStudentsAndTermFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add student_type to students table
        Schema::table('students', function (Blueprint $table) {
            $table->enum('student_type', ['day', 'boarding'])->default('day')->after('is_transferred');
        });

        // Add student_type to term_fees table to differentiate day/boarding fees
        Schema::table('term_fees', function (Blueprint $table) {
            $table->enum('student_type', ['day', 'boarding'])->default('day')->after('fee_type_id');
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
            $table->dropColumn('student_type');
        });

        Schema::table('term_fees', function (Blueprint $table) {
            $table->dropColumn('student_type');
        });
    }
}
