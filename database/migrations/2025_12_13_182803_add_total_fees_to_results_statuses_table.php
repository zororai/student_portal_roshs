<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalFeesToResultsStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('results_statuses', 'total_fees')) {
            Schema::table('results_statuses', function (Blueprint $table) {
                $table->decimal('total_fees', 10, 2)->default(0)->after('result_period');
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
        if (Schema::hasColumn('results_statuses', 'total_fees')) {
            Schema::table('results_statuses', function (Blueprint $table) {
                $table->dropColumn('total_fees');
            });
        }
    }
}
