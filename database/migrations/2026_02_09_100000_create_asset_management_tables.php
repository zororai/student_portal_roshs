<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetManagementTables extends Migration
{
    public function up()
    {
        // Asset Categories - groups similar assets and defines depreciation rules
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('useful_life_years')->default(5);
            $table->string('depreciation_method')->default('straight_line'); // straight_line, reducing_balance
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Asset Locations - physical or logical asset locations
        Schema::create('asset_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Assets - core asset records
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->string('serial_number')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 12, 2);
            $table->decimal('residual_value', 12, 2)->default(0);
            $table->decimal('current_value', 12, 2);
            $table->string('condition')->default('new'); // new, good, fair, damaged
            $table->string('status')->default('active'); // active, under_maintenance, disposed
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('assigned_type')->nullable(); // user, teacher, student, class
            $table->unsignedBigInteger('assigned_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->date('disposed_at')->nullable();
            $table->string('disposal_reason')->nullable();
            $table->decimal('disposal_value', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('asset_categories')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('asset_locations')->onDelete('set null');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['status', 'condition']);
            $table->index(['assigned_type', 'assigned_id']);
        });

        // Asset Maintenance - maintenance, repairs, and inspections
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->string('maintenance_type'); // repair, service, inspection
            $table->text('description');
            $table->date('reported_date');
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['asset_id', 'status']);
        });

        // Asset Depreciation - yearly depreciation records
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->integer('year');
            $table->decimal('opening_value', 12, 2);
            $table->decimal('depreciation_amount', 12, 2);
            $table->decimal('closing_value', 12, 2);
            $table->boolean('posted_to_ledger')->default(false);
            $table->unsignedBigInteger('ledger_entry_id')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('ledger_entry_id')->references('id')->on('ledger_entries')->onDelete('set null');

            $table->unique(['asset_id', 'year']);
            $table->index(['year', 'posted_to_ledger']);
        });

        // Asset Assignment History - immutable asset movement history
        Schema::create('asset_assignment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->string('from_type')->nullable();
            $table->unsignedBigInteger('from_id')->nullable();
            $table->string('to_type')->nullable();
            $table->unsignedBigInteger('to_id')->nullable();
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['asset_id', 'assigned_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_assignment_histories');
        Schema::dropIfExists('asset_depreciations');
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_locations');
        Schema::dropIfExists('asset_categories');
    }
}
