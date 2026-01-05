<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleFieldsToTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->boolean('is_class_teacher')->default(false)->after('permanent_address');
            $table->boolean('is_hod')->default(false)->after('is_class_teacher');
            $table->boolean('is_sport_director')->default(false)->after('is_hod');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['is_class_teacher', 'is_hod', 'is_sport_director']);
        });
    }
}
