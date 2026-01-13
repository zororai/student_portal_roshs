<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckoutReasonToTeacherAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_attendance', function (Blueprint $table) {
            $table->text('checkout_reason')->nullable()->after('check_out_time');
            $table->time('expected_checkout_time')->nullable()->after('checkout_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_attendance', function (Blueprint $table) {
            $table->dropColumn(['checkout_reason', 'expected_checkout_time']);
        });
    }
}
