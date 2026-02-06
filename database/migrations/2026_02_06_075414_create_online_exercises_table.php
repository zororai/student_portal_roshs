<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Main exercises table
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->enum('type', ['quiz', 'classwork', 'homework']);
            $table->integer('total_marks')->default(0);
            $table->integer('duration_minutes')->nullable();
            $table->datetime('due_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('show_results')->default(false);
            $table->string('term')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });

        // Questions table
        Schema::create('exercise_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'file_upload']);
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->integer('marks')->default(1);
            $table->integer('order')->default(0);
            $table->text('correct_answer')->nullable();
            $table->timestamps();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
        });

        // Question options for MCQ
        Schema::create('exercise_question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('exercise_questions')->onDelete('cascade');
        });

        // Student submissions
        Schema::create('exercise_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('student_id');
            $table->datetime('started_at')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'marked'])->default('not_started');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['exercise_id', 'student_id']);
        });

        // Student answers
        Schema::create('exercise_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submission_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer_text')->nullable();
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('submission_id')->references('id')->on('exercise_submissions')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('exercise_questions')->onDelete('cascade');
            $table->foreign('selected_option_id')->references('id')->on('exercise_question_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_answers');
        Schema::dropIfExists('exercise_submissions');
        Schema::dropIfExists('exercise_question_options');
        Schema::dropIfExists('exercise_questions');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('online_exercises');
    }
}
