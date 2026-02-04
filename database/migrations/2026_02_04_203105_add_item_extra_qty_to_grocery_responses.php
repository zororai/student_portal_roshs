<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemExtraQtyToGroceryResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->json('item_extra_qty')->nullable()->after('extra_items');
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
            $table->dropColumn('item_extra_qty');
        });
    }
}
