<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('administrative_regions', function (Blueprint $table) {
            $table->id(); // INT, PRIMARY
            $table->string('name');             // Tên tiếng Việt
            $table->string('name_en')->nullable(); // Tên tiếng Anh
            $table->string('code_name')->nullable();   // Mã tên viết liền không dấu
            $table->string('code_name_en')->nullable(); // Tên mã tiếng Anh
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_regions');
    }
};

