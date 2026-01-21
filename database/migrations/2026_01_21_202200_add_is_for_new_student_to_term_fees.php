<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsForNewStudentToTermFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('term_fees', function (Blueprint $table) {
            $table->boolean('is_for_new_student')->default(false)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('term_fees', function (Blueprint $table) {
            $table->dropColumn('is_for_new_student');
        });
    }
}
