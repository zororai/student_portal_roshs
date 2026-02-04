<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraItemsToGroceryResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->json('extra_items')->nullable()->after('items_bought');
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
            $table->dropColumn('extra_items');
        });
    }
}
