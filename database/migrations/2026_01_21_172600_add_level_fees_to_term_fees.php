<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelFeesToTermFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('term_fees', function (Blueprint $table) {
            $table->integer('class_level')->nullable()->after('curriculum_type');
        });

        // Create a separate table for level-based fee adjustments
        Schema::create('level_fee_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('results_status_id');
            $table->integer('class_level');
            $table->string('curriculum_type')->default('zimsec');
            $table->string('student_type')->default('day');
            $table->decimal('adjustment_amount', 10, 2)->default(0);
            $table->string('adjustment_type')->default('fixed'); // fixed or percentage
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('results_status_id')->references('id')->on('results_statuses')->onDelete('cascade');
            $table->unique(['results_status_id', 'class_level', 'curriculum_type', 'student_type'], 'level_fee_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('level_fee_adjustments');
        
        Schema::table('term_fees', function (Blueprint $table) {
            $table->dropColumn('class_level');
        });
    }
}
