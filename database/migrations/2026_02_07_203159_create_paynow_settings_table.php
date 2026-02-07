<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaynowSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paynow_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('paynow_id');
            $table->string('paynow_key');
            $table->boolean('is_active')->default(true);
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('return_url')->nullable();
            $table->string('result_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paynow_settings');
    }
}
