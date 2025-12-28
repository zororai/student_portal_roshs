<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrCodeToTeachersAndCreateTeacherLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add qr_code column to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->after('permanent_address');
            $table->string('qr_code_token')->unique()->nullable()->after('qr_code');
        });

        // Create teacher_logs table for attendance tracking
        Schema::create('teacher_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->date('log_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['present', 'absent', 'partial'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->unique(['teacher_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_logs');

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'qr_code_token']);
        });
    }
}
