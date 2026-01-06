<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeSubjectCodeToString extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE subjects MODIFY subject_code VARCHAR(20)');
    }

    public function down()
    {
        DB::statement('ALTER TABLE subjects MODIFY subject_code BIGINT UNSIGNED');
    }
}
