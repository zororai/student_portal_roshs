<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToTeacherLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_logs', function (Blueprint $table) {
            $table->decimal('check_in_lat', 10, 8)->nullable()->after('time_in');
            $table->decimal('check_in_lng', 11, 8)->nullable()->after('check_in_lat');
            $table->decimal('check_out_lat', 10, 8)->nullable()->after('time_out');
            $table->decimal('check_out_lng', 11, 8)->nullable()->after('check_out_lat');
            $table->boolean('within_boundary')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_logs', function (Blueprint $table) {
            $table->dropColumn(['check_in_lat', 'check_in_lng', 'check_out_lat', 'check_out_lng', 'within_boundary']);
        });
    }
}
