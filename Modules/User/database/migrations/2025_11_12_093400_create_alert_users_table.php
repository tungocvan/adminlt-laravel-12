<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alert_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // người nhận
            $table->string('title'); // tiêu đề message
            $table->text('content'); // nội dung message
            $table->boolean('is_read')->default(false); // đánh dấu đã đọc hay chưa
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_users');
    }
};

