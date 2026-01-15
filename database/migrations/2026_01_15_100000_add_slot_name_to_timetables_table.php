<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotNameToTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->string('slot_name')->nullable()->after('slot_type');
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
            $table->dropColumn('slot_name');
        });
    }
}
