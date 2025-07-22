<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id(); // hoặc dùng $table->string('code')->primary(); nếu 'code' là khóa chính
            $table->string('code')->unique(); // '01', '02', ...
            $table->string('name'); // 'Hà Nội'
            $table->string('name_en')->nullable(); // 'Ha Noi'
            $table->string('full_name')->nullable(); // 'Thành phố Hà Nội'
            $table->string('full_name_en')->nullable(); // 'Ha Noi City'
            $table->string('code_name')->nullable(); // 'ha_noi'
            $table->unsignedBigInteger('administrative_unit_id')->nullable();
            $table->timestamps();

            // Khóa ngoại (nếu có bảng administrative_units)
            //$table->foreign('administrative_unit_id')->references('id')->on('administrative_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};

