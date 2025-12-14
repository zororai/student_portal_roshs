<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroceryListsTable extends Migration
{
    public function up()
    {
        Schema::create('grocery_lists', function (Blueprint $table) {
            $table->id();
            $table->string('term');
            $table->string('year');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('grocery_list_class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grocery_list_id');
            $table->unsignedBigInteger('class_id');
            $table->timestamps();

            $table->foreign('grocery_list_id')->references('id')->on('grocery_lists')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('grades')->onDelete('cascade');
        });

        Schema::create('grocery_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grocery_list_id');
            $table->string('name');
            $table->string('quantity')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('grocery_list_id')->references('id')->on('grocery_lists')->onDelete('cascade');
        });

        Schema::create('grocery_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grocery_list_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('parent_id');
            $table->json('items_bought')->nullable();
            $table->boolean('submitted')->default(false);
            $table->boolean('acknowledged')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('grocery_list_id')->references('id')->on('grocery_lists')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            
            $table->unique(['grocery_list_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('grocery_responses');
        Schema::dropIfExists('grocery_items');
        Schema::dropIfExists('grocery_list_class');
        Schema::dropIfExists('grocery_lists');
    }
}
