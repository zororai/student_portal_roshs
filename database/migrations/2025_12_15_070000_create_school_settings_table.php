<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_type')->default('text');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create class_formats table to store the class naming formats
        Schema::create('class_formats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('format_name');
            $table->integer('numeric_value');
            $table->string('display_name');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('school_settings')->insert([
            [
                'setting_key' => 'upgrade_direction',
                'setting_value' => 'ascending',
                'setting_type' => 'select',
                'description' => 'Direction of class upgrade (ascending: 1->2->3 or descending: 3->2->1)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'class_format_type',
                'setting_value' => 'numeric',
                'setting_type' => 'select',
                'description' => 'Type of class naming format (numeric, alphabetic, custom)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_formats');
        Schema::dropIfExists('school_settings');
    }
}
