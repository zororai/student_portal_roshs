<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsAndSalesTables extends Migration
{
    public function up()
    {
        // Add missing columns to existing products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->string('sku')->unique()->nullable()->after('name');
                }
                if (!Schema::hasColumn('products', 'barcode')) {
                    $table->string('barcode')->unique()->nullable()->after('sku');
                }
                if (!Schema::hasColumn('products', 'image')) {
                    $table->string('image')->nullable()->after('category');
                }
                if (!Schema::hasColumn('products', 'quantity')) {
                    $table->integer('quantity')->default(0)->after('cost_price');
                }
                if (!Schema::hasColumn('products', 'min_stock_level')) {
                    $table->integer('min_stock_level')->default(5)->after('quantity');
                }
                if (!Schema::hasColumn('products', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('min_stock_level');
                }
                if (!Schema::hasColumn('products', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                }
            });
        } else {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('sku')->unique()->nullable();
                $table->string('barcode')->unique();
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->string('image')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('cost_price', 15, 2)->default(0);
                $table->integer('quantity')->default(0);
                $table->integer('min_stock_level')->default(5);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Product Sales table
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->date('sale_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('change_given', 15, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('sold_by');
            $table->timestamps();
            $table->foreign('sold_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Product Sale Items table
        Schema::create('product_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_sale_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->string('barcode');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
            $table->foreign('product_sale_id')->references('id')->on('product_sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        // Stock movements for tracking
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reason')->nullable();
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('product_sale_items');
        Schema::dropIfExists('product_sales');
        Schema::dropIfExists('products');
    }
}
