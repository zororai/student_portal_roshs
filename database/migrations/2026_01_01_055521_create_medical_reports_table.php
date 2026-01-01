<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->string('condition_type');
            $table->string('condition_name');
            $table->text('description');
            $table->text('medications')->nullable();
            $table->text('emergency_instructions')->nullable();
            $table->date('diagnosis_date')->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('doctor_contact')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending', 'acknowledged', 'reviewed'])->default('pending');
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('admin_response')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('acknowledged_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_reports');
    }
}
