<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsPayableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Suppliers table - Skip if already exists
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('tax_number')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Supplier Invoices table
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_invoice_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('expense_type', ['expense', 'asset'])->default('expense');
            $table->string('expense_category')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });

        // Supplier Payments table
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->unsignedBigInteger('supplier_invoice_id');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('supplier_invoice_id')->references('id')->on('supplier_invoices')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('suppliers');
    }
}
