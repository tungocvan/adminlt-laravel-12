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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stt_tt20_2022')->nullable();
            $table->string('phan_nhom_tt15',20)->nullable();
            $table->string('ten_hoat_chat')->nullable();
            $table->string('nong_do_ham_luong', 150)->nullable();
            $table->string('ten_biet_duoc')->nullable();
            $table->string('dang_bao_che', 150)->nullable();
            $table->string('duong_dung', 50)->nullable();
            $table->string('don_vi_tinh', 50)->nullable();
            $table->string('quy_cach_dong_goi', 255)->nullable();
            $table->string('giay_phep_luu_hanh', 100)->nullable();
            $table->string('han_dung', 255)->nullable();
            $table->string('co_so_san_xuat',255)->nullable();
            $table->string('nuoc_san_xuat', 100)->nullable();
            $table->unsignedBigInteger('gia_ke_khai')->nullable();
            $table->unsignedBigInteger('don_gia')->nullable();
            $table->unsignedBigInteger('gia_von')->nullable();
            $table->boolean('trang_thai_trung_thau')->default(false);
            $table->string('nha_phan_phoi')->nullable();
            $table->string('nhom_thuoc')->nullable();
            $table->string('link_hinh_anh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
