<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeParentIdNullableInGroceryResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign key first
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        // Use raw SQL to modify column to nullable
        DB::statement('ALTER TABLE grocery_responses MODIFY parent_id BIGINT UNSIGNED NULL');

        // Add foreign key back with SET NULL on delete
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        DB::statement('ALTER TABLE grocery_responses MODIFY parent_id BIGINT UNSIGNED NOT NULL');

        Schema::table('grocery_responses', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
        });
    }
}
