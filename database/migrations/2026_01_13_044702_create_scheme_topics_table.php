<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemeTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scheme_id');
            $table->unsignedBigInteger('syllabus_topic_id');
            $table->integer('week_number')->nullable();
            $table->integer('planned_periods')->default(1);
            $table->integer('actual_periods')->default(0);
            $table->decimal('expected_performance', 5, 2)->nullable(); // Expected avg %
            $table->decimal('actual_performance', 5, 2)->nullable(); // Actual avg % from assessments
            $table->enum('mastery_level', ['not_assessed', 'weak', 'partial', 'mastered'])->default('not_assessed');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'needs_remedial'])->default('pending');
            $table->text('teaching_methods')->nullable();
            $table->text('resources')->nullable();
            $table->text('remarks')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('remedial_required')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->foreign('scheme_id')->references('id')->on('schemes_of_work')->onDelete('cascade');
            $table->foreign('syllabus_topic_id')->references('id')->on('syllabus_topics')->onDelete('cascade');
            
            $table->index(['scheme_id', 'week_number']);
            $table->unique(['scheme_id', 'syllabus_topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheme_topics');
    }
}
