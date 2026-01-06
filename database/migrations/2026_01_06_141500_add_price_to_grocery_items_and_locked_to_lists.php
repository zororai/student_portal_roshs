<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToGroceryItemsAndLockedToLists extends Migration
{
    public function up()
    {
        // Add price to grocery_items
        Schema::table('grocery_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('quantity');
        });

        // Add locked field to grocery_lists
        Schema::table('grocery_lists', function (Blueprint $table) {
            $table->boolean('locked')->default(false)->after('status');
            $table->timestamp('locked_at')->nullable()->after('locked');
        });
    }

    public function down()
    {
        Schema::table('grocery_items', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('grocery_lists', function (Blueprint $table) {
            $table->dropColumn(['locked', 'locked_at']);
        });
    }
}
