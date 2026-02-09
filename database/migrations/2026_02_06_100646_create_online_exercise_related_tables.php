<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineExerciseRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip if online_exercises table doesn't exist (exercises table is used instead)
        if (!Schema::hasTable('online_exercises')) {
            return;
        }
        
        // Questions table
        if (!Schema::hasTable('online_exercise_questions')) {
            Schema::create('online_exercise_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exercise_id');
                $table->string('question_type')->default('multiple_choice');
                $table->text('question_text');
                $table->string('question_image')->nullable();
                $table->integer('marks')->default(1);
                $table->integer('order')->default(0);
                $table->text('correct_answer')->nullable();
                $table->timestamps();

                $table->foreign('exercise_id')->references('id')->on('online_exercises')->onDelete('cascade');
            });
        }

        // Question options for MCQ
        if (!Schema::hasTable('online_exercise_question_options')) {
            Schema::create('online_exercise_question_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('question_id');
                $table->string('option_text');
                $table->boolean('is_correct')->default(false);
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->foreign('question_id')->references('id')->on('online_exercise_questions')->onDelete('cascade');
            });
        }

        // Student answers
        if (!Schema::hasTable('online_exercise_answers')) {
            Schema::create('online_exercise_answers', function (Blueprint $table) {
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

                $table->foreign('submission_id')->references('id')->on('online_exercise_submissions')->onDelete('cascade');
                $table->foreign('question_id')->references('id')->on('online_exercise_questions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_exercise_answers');
        Schema::dropIfExists('online_exercise_question_options');
        Schema::dropIfExists('online_exercise_questions');
    }
}
