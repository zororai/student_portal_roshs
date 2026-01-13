<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemedialLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remedial_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('syllabus_topic_id');
            $table->unsignedBigInteger('scheme_topic_id')->nullable();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->default(40);
            $table->enum('status', ['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('trigger_type', ['auto', 'manual'])->default('auto'); // auto = system triggered, manual = teacher created
            $table->decimal('trigger_score', 5, 2)->nullable(); // The score that triggered remedial
            $table->decimal('pre_remedial_avg', 5, 2)->nullable();
            $table->decimal('post_remedial_avg', 5, 2)->nullable();
            $table->text('resources')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->timestamp('parent_notified_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('syllabus_topic_id')->references('id')->on('syllabus_topics')->onDelete('cascade');
            $table->foreign('scheme_topic_id')->references('id')->on('scheme_topics')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            
            $table->index(['class_id', 'subject_id', 'status']);
            $table->index(['teacher_id', 'status']);
        });

        // Pivot table for students assigned to remedial lessons
        Schema::create('remedial_lesson_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('remedial_lesson_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('pre_score', 5, 2)->nullable();
            $table->decimal('post_score', 5, 2)->nullable();
            $table->boolean('attended')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('remedial_lesson_id')->references('id')->on('remedial_lessons')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            $table->unique(['remedial_lesson_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remedial_lesson_student');
        Schema::dropIfExists('remedial_lessons');
    }
}
