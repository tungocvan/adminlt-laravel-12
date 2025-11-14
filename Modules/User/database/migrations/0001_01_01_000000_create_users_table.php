<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Khóa chính');
        
            $table->string('name')->nullable()->comment('Tên hiển thị của người dùng');
            $table->string('email')->unique()->comment('Địa chỉ email duy nhất');
            $table->timestamp('email_verified_at')->nullable()->comment('Thời điểm xác minh email');
        
            $table->string('username')->unique()->nullable()->comment('Tên đăng nhập (tự động sinh từ email nếu bỏ trống)');
            $table->string('password')->comment('Mật khẩu được mã hóa');
        
            $table->tinyInteger('is_admin')->default(0)->comment('0 = user thường, 1 = admin');
            $table->date('birthdate')->nullable()->comment('Ngày sinh của người dùng');
        
            $table->string('google_id')->nullable()->comment('ID tài khoản Google (nếu đăng nhập bằng Google)');
            $table->string('device_token')->nullable()->comment('Token của thiết bị để gửi thông báo đẩy');
            $table->string('referral_code')->nullable()->comment('Mã giới thiệu duy nhất cho người dùng');
        
            $table->rememberToken()->comment('Token ghi nhớ đăng nhập');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
