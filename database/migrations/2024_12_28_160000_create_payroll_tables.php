<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollTables extends Migration
{
    public function up()
    {
        // Employee Salaries - base salary configuration
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('medical_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);
            $table->decimal('tax_deduction', 12, 2)->default(0);
            $table->decimal('pension_deduction', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, cash, mobile_money
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Payroll Records - monthly payroll entries
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('salary_id');
            $table->string('pay_period'); // e.g., "2024-12" for December 2024
            $table->date('pay_date');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('total_allowances', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2);
            $table->decimal('net_salary', 12, 2);
            $table->integer('days_worked')->default(0);
            $table->integer('days_absent')->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('salary_id')->references('id')->on('employee_salaries')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['user_id', 'pay_period']);
        });

        // Cash Book - daily cash transactions
        Schema::create('cash_book_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('entry_date');
            $table->string('reference_number')->unique();
            $table->string('transaction_type'); // receipt, payment
            $table->string('category'); // fees, salary, utilities, supplies, etc.
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('payment_method')->nullable(); // cash, bank, mobile_money
            $table->string('payer_payee')->nullable(); // who paid or received
            $table->unsignedBigInteger('related_payroll_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('related_payroll_id')->references('id')->on('payrolls')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Ledger Accounts
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->string('account_type'); // asset, liability, equity, income, expense
            $table->string('category')->nullable(); // sub-category
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Ledger Entries - double-entry bookkeeping
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('entry_date');
            $table->string('reference_number');
            $table->unsignedBigInteger('account_id');
            $table->string('entry_type'); // debit, credit
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->unsignedBigInteger('cash_book_entry_id')->nullable();
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('ledger_accounts')->onDelete('cascade');
            $table->foreign('cash_book_entry_id')->references('id')->on('cash_book_entries')->onDelete('set null');
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('ledger_accounts');
        Schema::dropIfExists('cash_book_entries');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employee_salaries');
    }
}
