<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');                        // Tên hiển thị
            $table->string('slug')->nullable()->unique(); // Slug (nếu là danh mục)
            $table->string('url')->nullable();            // Link (nếu là menu)
            $table->string('icon')->nullable();           // Icon (menu hiển thị)
            $table->string('can')->nullable();            // Quyền hạn (permission key)
            $table->enum('type', ['category', 'menu'])->default('category');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
