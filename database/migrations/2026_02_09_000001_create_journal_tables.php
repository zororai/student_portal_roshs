<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Journal Batches table
        Schema::create('journal_batches', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->text('description');
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'posted'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('posted_by')->references('id')->on('users');
        });

        // Journal Entries table
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_batch_id');
            $table->unsignedBigInteger('ledger_account_id');
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->text('narration');
            $table->timestamps();

            $table->foreign('journal_batch_id')->references('id')->on('journal_batches')->onDelete('cascade');
            $table->foreign('ledger_account_id')->references('id')->on('ledger_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journal_batches');
    }
}
