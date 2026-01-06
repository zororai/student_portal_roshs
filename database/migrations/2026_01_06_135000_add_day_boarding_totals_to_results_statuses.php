<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDayBoardingTotalsToResultsStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('results_statuses', function (Blueprint $table) {
            $table->decimal('total_day_fees', 10, 2)->default(0)->after('total_fees');
            $table->decimal('total_boarding_fees', 10, 2)->default(0)->after('total_day_fees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('results_statuses', function (Blueprint $table) {
            $table->dropColumn(['total_day_fees', 'total_boarding_fees']);
        });
    }
}
