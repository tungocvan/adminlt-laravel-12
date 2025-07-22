<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id(); // Hoặc nếu dùng 'code' làm khóa chính thì sửa lại
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('full_name')->nullable();
            $table->string('full_name_en')->nullable();
            $table->string('code_name')->nullable();
            $table->string('province_code'); // Khóa ngoại gián tiếp (code thay vì id)
            $table->unsignedBigInteger('administrative_unit_id')->nullable();
            $table->timestamps();

            // (Tùy chọn) Nếu muốn liên kết với bảng provinces qua 'code'
            // hoặc bạn sẽ join thủ công bằng province_code
            // hoặc nếu bạn có bảng `provinces` dùng `code` là unique, bạn có thể tạo:
            // $table->foreign('province_code')->references('code')->on('provinces')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};

