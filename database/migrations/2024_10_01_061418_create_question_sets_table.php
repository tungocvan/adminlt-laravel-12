<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionSetsTable extends Migration
{
    public function up()
    {
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name'); // bộ đề
            $table->string('category_topic_id'); // môn học
            $table->string('category_class_id'); // khối lớp
            $table->enum('question_type', ['Đúng/Sai', 'Một đáp án', 'Nhiều đáp án', 'Tự luận']);
            $table->text('questions');
            $table->unsignedInteger('total_questions');            
            $table->unsignedInteger('timeRemaining');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_sets');
    }
}

