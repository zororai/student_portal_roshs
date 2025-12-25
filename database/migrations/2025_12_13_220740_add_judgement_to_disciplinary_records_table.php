<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJudgementToDisciplinaryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('disciplinary_records', 'judgement')) {
            Schema::table('disciplinary_records', function (Blueprint $table) {
                $table->text('judgement')->nullable()->after('description');
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
        if (Schema::hasColumn('disciplinary_records', 'judgement')) {
            Schema::table('disciplinary_records', function (Blueprint $table) {
                $table->dropColumn('judgement');
            });
        }
    }
}
