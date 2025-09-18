<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wp_products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();          // Tên sản phẩm
            $table->string('slug')->unique();          // Slug
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();

            $table->string('image')->nullable();       // Ảnh chính
            $table->json('gallery')->nullable();       // Ảnh phụ
            $table->json('tags')->nullable();          // Tags dạng JSON

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wp_products');
    }
};
