<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyllabusTopicToAssessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->unsignedBigInteger('syllabus_topic_id')->nullable()->after('subject_id');
            $table->string('term')->nullable()->after('due_date');
            $table->string('academic_year')->nullable()->after('term');
            
            $table->foreign('syllabus_topic_id')->references('id')->on('syllabus_topics')->onDelete('set null');
            $table->index(['syllabus_topic_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['syllabus_topic_id']);
            $table->dropIndex(['syllabus_topic_id', 'class_id']);
            $table->dropColumn(['syllabus_topic_id', 'term', 'academic_year']);
        });
    }
}
