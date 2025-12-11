<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegistrationTokenToParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parents', function (Blueprint $table) {
            if (!Schema::hasColumn('parents', 'registration_token')) {
                $table->string('registration_token', 100)->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('parents', 'token_expires_at')) {
                $table->timestamp('token_expires_at')->nullable()->after('permanent_address');
            }
            if (!Schema::hasColumn('parents', 'registration_completed')) {
                $table->boolean('registration_completed')->default(false)->after('permanent_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['registration_token', 'token_expires_at', 'registration_completed']);
        });
    }
}
