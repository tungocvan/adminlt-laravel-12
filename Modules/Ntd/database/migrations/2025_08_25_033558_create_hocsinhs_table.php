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
      
        Schema::create('hocsinhs', function (Blueprint $table) {
            $table->id();
            $table->integer('stt')->nullable();
            $table->string('lop')->nullable();
            $table->string('ho_va_ten');
            $table->date('ngay_sinh')->nullable();
            $table->string('gioi_tinh')->nullable();
            $table->string('ma_dinh_danh_hoc_sinh')->unique();
            $table->string('gvcn')->nullable();
            $table->string('bao_mau')->nullable();
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hocsinhs');
    }
};
