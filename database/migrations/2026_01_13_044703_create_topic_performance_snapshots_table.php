<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicPerformanceSnapshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('syllabus_topic_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('term');
            $table->string('academic_year');
            $table->integer('students_assessed')->default(0);
            $table->integer('total_students')->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->decimal('highest_score', 5, 2)->nullable();
            $table->decimal('lowest_score', 5, 2)->nullable();
            $table->decimal('pass_rate', 5, 2)->default(0); // % of students >= 50%
            $table->enum('mastery_level', ['weak', 'partial', 'mastered'])->default('weak');
            $table->integer('assessments_count')->default(0);
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->foreign('syllabus_topic_id')->references('id')->on('syllabus_topics')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            
            $table->index(['syllabus_topic_id', 'class_id', 'term'], 'topic_perf_topic_class_term_idx');
            $table->index(['class_id', 'subject_id', 'academic_year'], 'topic_perf_class_subject_year_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_performance_snapshots');
    }
}
