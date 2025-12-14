<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('school_applying_for')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->string('applying_for_form');
            $table->string('religion')->nullable();
            $table->string('street_address')->nullable();
            $table->string('residential_area')->nullable();
            $table->json('subjects_of_interest')->nullable();
            $table->string('guardian_full_name');
            $table->string('guardian_phone');
            $table->string('guardian_email')->nullable();
            $table->string('guardian_relationship');
            $table->string('birth_entry_number')->nullable();
            $table->string('dream_job')->nullable();
            $table->date('expected_start_date')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_applications');
    }
}
