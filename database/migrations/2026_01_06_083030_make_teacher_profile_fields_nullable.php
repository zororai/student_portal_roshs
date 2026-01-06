<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeTeacherProfileFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE teachers MODIFY dateofbirth DATE NULL');
        DB::statement('ALTER TABLE teachers MODIFY current_address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE teachers MODIFY permanent_address VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE teachers MODIFY dateofbirth DATE NOT NULL');
        DB::statement('ALTER TABLE teachers MODIFY current_address VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE teachers MODIFY permanent_address VARCHAR(255) NOT NULL');
    }
}
