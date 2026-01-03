<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Timetable settings per class
        Schema::create('timetable_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->time('start_time');
            $table->time('break_start');
            $table->time('break_end');
            $table->time('lunch_start');
            $table->time('lunch_end');
            $table->time('end_time');
            $table->integer('subject_duration')->default(40); // minutes
            $table->string('academic_year')->nullable();
            $table->string('term')->nullable();
            $table->timestamps();
            
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
        });

        // Individual timetable slots
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('slot_type', ['subject', 'break', 'lunch', 'free'])->default('subject');
            $table->integer('slot_order')->default(0);
            $table->string('academic_year')->nullable();
            $table->integer('term')->nullable();
            $table->timestamps();
            
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('timetable_settings');
    }
}
