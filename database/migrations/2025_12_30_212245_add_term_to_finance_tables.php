<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermToFinanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_book_entries', function (Blueprint $table) {
            $table->string('term')->nullable()->after('entry_date');
            $table->year('year')->nullable()->after('term');
        });

        Schema::table('school_incomes', function (Blueprint $table) {
            $table->string('term')->nullable()->after('date');
            $table->year('year')->nullable()->after('term');
        });

        Schema::table('school_expenses', function (Blueprint $table) {
            $table->string('term')->nullable()->after('date');
            $table->year('year')->nullable()->after('term');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('term')->nullable()->after('expense_date');
            $table->year('year')->nullable()->after('term');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('term')->nullable()->after('created_at');
            $table->year('year')->nullable()->after('term');
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->string('term')->nullable()->after('entry_date');
            $table->year('year')->nullable()->after('term');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_book_entries', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });

        Schema::table('school_incomes', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });

        Schema::table('school_expenses', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn(['term', 'year']);
        });
    }
}
