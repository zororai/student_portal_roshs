<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginAttemptsTable extends Migration
{
    public function up()
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('email')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->string('failure_reason')->nullable();
            $table->boolean('captcha_required')->default(false);
            $table->boolean('captcha_passed')->default(false);
            $table->timestamps();

            $table->index(['ip_address', 'created_at']);
            $table->index(['email', 'created_at']);
        });

        Schema::create('login_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->string('email')->nullable();
            $table->string('lockout_type'); // 'ip' or 'account'
            $table->timestamp('locked_until');
            $table->integer('attempt_count')->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['ip_address', 'locked_until']);
            $table->index(['email', 'locked_until']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_lockouts');
        Schema::dropIfExists('login_attempts');
    }
}
