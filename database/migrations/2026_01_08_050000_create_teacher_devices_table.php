<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->string('device_id')->unique();
            $table->string('device_name')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('status', ['active', 'revoked', 'pending'])->default('pending');
            $table->timestamp('registered_at')->nullable();
            $table->unsignedBigInteger('registered_by')->nullable();
            $table->unsignedBigInteger('revoked_by')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('revoked_by')->references('id')->on('users')->onDelete('set null');
        });

        // Add device_registration_status to teachers table
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('device_registration_status', ['not_required', 'pending', 'registered'])->default('not_required')->after('qr_code_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('device_registration_status');
        });

        Schema::dropIfExists('teacher_devices');
    }
}
