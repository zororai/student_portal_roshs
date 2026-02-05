<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherSupportToLibraryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('library_records', function (Blueprint $table) {
            $table->string('borrower_type')->default('student')->after('id'); // 'student' or 'teacher'
            $table->unsignedBigInteger('teacher_id')->nullable()->after('student_id');
            
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('library_records', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['borrower_type', 'teacher_id']);
        });
    }
}
