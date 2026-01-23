<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeLevelGroupsAndStructures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fee Level Groups - defines groups of classes with same fee structure
        // e.g., "Junior (Form 1-4)", "Senior (Form 5-6)", "Primary (Grade 1-7)", "ECD"
        Schema::create('fee_level_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Junior", "Senior", "Primary", "ECD"
            $table->string('description')->nullable();
            $table->integer('min_class_numeric')->nullable(); // e.g., 1 for Form 1
            $table->integer('max_class_numeric')->nullable(); // e.g., 4 for Form 4
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fee Structures - stores complete fee breakdown per term, level group, student type, and new/existing status
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('results_status_id'); // The term
            $table->unsignedBigInteger('fee_level_group_id'); // The level group
            $table->unsignedBigInteger('fee_type_id'); // The fee type (tuition, levy, etc.)
            $table->string('student_type'); // 'day' or 'boarding'
            $table->string('curriculum_type')->default('zimsec'); // 'zimsec' or 'cambridge'
            $table->boolean('is_for_new_student')->default(false); // true for new students only
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('results_status_id')->references('id')->on('results_statuses')->onDelete('cascade');
            $table->foreign('fee_level_group_id')->references('id')->on('fee_level_groups')->onDelete('cascade');
            $table->foreign('fee_type_id')->references('id')->on('fee_types')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(
                ['results_status_id', 'fee_level_group_id', 'fee_type_id', 'student_type', 'curriculum_type', 'is_for_new_student'],
                'fee_structure_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_level_groups');
    }
}
