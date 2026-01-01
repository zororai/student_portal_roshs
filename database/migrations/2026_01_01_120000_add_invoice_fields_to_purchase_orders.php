<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFieldsToPurchaseOrders extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('status');
            $table->date('invoice_date')->nullable()->after('invoice_number');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('invoice_date');
            $table->date('payment_date')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_date');
            $table->decimal('amount_paid', 15, 2)->default(0)->after('payment_method');
            $table->unsignedBigInteger('expense_id')->nullable()->after('amount_paid');
            $table->unsignedBigInteger('cashbook_entry_id')->nullable()->after('expense_id');
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'invoice_date', 
                'payment_status',
                'payment_date',
                'payment_method',
                'amount_paid',
                'expense_id',
                'cashbook_entry_id'
            ]);
        });
    }
}
