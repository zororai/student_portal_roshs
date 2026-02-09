<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsReceivableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Student Accounts table
        Schema::create('student_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique('student_id');
        });

        // Student Invoices table
        Schema::create('student_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('student_id');
            $table->string('term')->nullable();
            $table->integer('year');
            $table->decimal('amount', 15, 2);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });

        // Student Invoice Items table
        Schema::create('student_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('student_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_invoice_items');
        Schema::dropIfExists('student_invoices');
        Schema::dropIfExists('student_accounts');
    }
}
