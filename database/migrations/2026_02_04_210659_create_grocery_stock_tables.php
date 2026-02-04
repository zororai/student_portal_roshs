<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroceryStockTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Stock items master list
        Schema::create('grocery_stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('units'); // kg, litres, packets, etc.
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Stock transactions (receipts, usage, bad stock, balance brought forward)
        Schema::create('grocery_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('grocery_stock_items')->onDelete('cascade');
            $table->enum('type', ['received', 'usage', 'bad_stock', 'balance_bf', 'adjustment']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('term')->nullable();
            $table->integer('year')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('transaction_date');
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
        Schema::dropIfExists('grocery_stock_transactions');
        Schema::dropIfExists('grocery_stock_items');
    }
}
