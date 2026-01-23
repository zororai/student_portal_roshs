<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFeeStructureIdToStudentPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_payments', function (Blueprint $table) {
            // Add fee_structure_id column for new fee structure system
            $table->unsignedBigInteger('fee_structure_id')->nullable()->after('term_fee_id');
            
            // Add foreign key constraint
            $table->foreign('fee_structure_id')
                  ->references('id')
                  ->on('fee_structures')
                  ->onDelete('cascade');
        });

        // Make term_fee_id nullable using raw SQL (avoids doctrine/dbal requirement)
        DB::statement('ALTER TABLE student_payments MODIFY term_fee_id BIGINT UNSIGNED NULL');
        
        // Drop the foreign key constraint on term_fee_id so it can accept null values
        // First check if foreign key exists before trying to drop it
        try {
            Schema::table('student_payments', function (Blueprint $table) {
                $table->dropForeign(['term_fee_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist or have different name, ignore
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_payments', function (Blueprint $table) {
            $table->dropForeign(['fee_structure_id']);
            $table->dropColumn('fee_structure_id');
        });
    }
}
