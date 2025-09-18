<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('wp_products')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['category_id', 'product_id']); // dùng composite primary key thay vì id
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
