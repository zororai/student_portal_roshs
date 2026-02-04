<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemActualQtyToGroceryResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->json('item_actual_qty')->nullable()->after('item_short_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->dropColumn('item_actual_qty');
        });
    }
}
