<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bang_bao_gia', function (Blueprint $table) {
            $table->id();
            $table->string('ma_so')->unique()->comment('Mã số bảng báo giá');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Người tạo');
            $table->unsignedBigInteger('customer_id')->nullable()->comment('Khách hàng');
            $table->string('ten_khach_hang')->nullable();
            $table->string('file_path')->nullable()->comment('Đường dẫn file Excel đã tạo');
            $table->json('product_ids')->nullable()->comment('Danh sách ID sản phẩm');
            $table->text('ghi_chu')->nullable();
            $table->timestamp('exported_at')->nullable()->comment('Thời điểm tạo bảng báo giá');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bang_bao_gia');
    }
};
