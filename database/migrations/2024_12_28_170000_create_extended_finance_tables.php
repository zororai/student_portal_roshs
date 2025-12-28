<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtendedFinanceTables extends Migration
{
    public function up()
    {
        // Bank Reconciliation
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer', 'fee', 'interest']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_reconciled')->default(false);
            $table->unsignedBigInteger('reconciled_with')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->unsignedBigInteger('reconciled_by')->nullable();
            $table->timestamps();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreign('reconciled_with')->references('id')->on('cash_book_entries')->onDelete('set null');
            $table->foreign('reconciled_by')->references('id')->on('users')->onDelete('set null');
        });

        // Expense Management
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('expense_categories')->onDelete('set null');
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->date('expense_date');
            $table->unsignedBigInteger('category_id');
            $table->string('vendor_name')->nullable();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'mobile_money'])->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Suppliers/Vendors
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

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->enum('status', ['draft', 'pending', 'approved', 'ordered', 'received', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->string('unit')->default('pcs');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->integer('received_quantity')->default(0);
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });

        // Budgeting
        Schema::create('budget_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('period_type', ['annual', 'term', 'quarterly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_period_id');
            $table->enum('type', ['income', 'expense']);
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('budgeted_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('budget_period_id')->references('id')->on('budget_periods')->onDelete('cascade');
        });

        // Revenue Forecasting
        Schema::create('revenue_forecasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_period_id');
            $table->string('source');
            $table->decimal('expected_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->text('assumptions')->nullable();
            $table->timestamps();
            $table->foreign('budget_period_id')->references('id')->on('budget_periods')->onDelete('cascade');
        });

        // Expense Forecasting
        Schema::create('expense_forecasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_period_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_name');
            $table->decimal('expected_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->text('assumptions')->nullable();
            $table->timestamps();
            $table->foreign('budget_period_id')->references('id')->on('budget_periods')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_forecasts');
        Schema::dropIfExists('revenue_forecasts');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budget_periods');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_accounts');
    }
}
