<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('administrative_units', function (Blueprint $table) {
            $table->id(); // INT, PRIMARY
            $table->string('full_name');             // Tên đầy đủ tiếng Việt
            $table->string('full_name_en')->nullable();  // Tên đầy đủ tiếng Anh
            $table->string('short_name')->nullable();    // Tên rút gọn tiếng Việt
            $table->string('short_name_en')->nullable(); // Tên rút gọn tiếng Anh
            $table->string('code_name')->nullable();     // Mã không dấu
            $table->string('code_name_en')->nullable();  // Mã tiếng Anh
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_units');
    }
};

