<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsStatusTable extends Migration
{
    public function up()
    {
        Schema::create('results_statuses', function (Blueprint $table) {
            $table->id();
        
          
            $table->year('year');
            $table->enum('result_period', ['first', 'second', 'third']); // To track the three periods

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('results_status');
    }
}
