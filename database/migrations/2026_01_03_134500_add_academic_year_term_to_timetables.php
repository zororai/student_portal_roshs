<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcademicYearTermToTimetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (!Schema::hasColumn('timetables', 'academic_year')) {
                $table->string('academic_year')->nullable()->after('slot_order');
            }
            if (!Schema::hasColumn('timetables', 'term')) {
                $table->integer('term')->nullable()->after('academic_year');
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
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
            if (Schema::hasColumn('timetables', 'term')) {
                $table->dropColumn('term');
            }
        });
    }
}
