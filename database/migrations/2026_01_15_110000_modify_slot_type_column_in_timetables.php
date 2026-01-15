<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifySlotTypeColumnInTimetables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change slot_type from ENUM to VARCHAR to support new types
        DB::statement("ALTER TABLE timetables MODIFY COLUMN slot_type VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original ENUM if needed
        DB::statement("ALTER TABLE timetables MODIFY COLUMN slot_type ENUM('subject', 'break', 'lunch') NOT NULL");
    }
}
