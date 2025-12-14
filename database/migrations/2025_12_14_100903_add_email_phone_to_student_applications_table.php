<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailPhoneToStudentApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->string('student_email')->nullable()->after('last_name');
            $table->string('student_phone')->nullable()->after('student_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['student_email', 'student_phone']);
        });
    }
}
