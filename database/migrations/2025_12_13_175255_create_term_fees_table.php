<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('results_status_id');
            $table->unsignedBigInteger('fee_type_id');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            
            $table->foreign('results_status_id')->references('id')->on('results_statuses')->onDelete('cascade');
            $table->foreign('fee_type_id')->references('id')->on('fee_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_fees');
    }
}
