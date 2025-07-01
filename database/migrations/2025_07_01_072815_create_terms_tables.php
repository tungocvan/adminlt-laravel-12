<?php
// File: database/migrations/2025_07_01_000000_create_terms_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('term_taxonomy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term_id');
            $table->string('taxonomy'); // category, tag, ...            
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('count')->default(0);
            $table->unsignedBigInteger('position')->default(0); // vị trí sắp xếp
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_taxonomy');
        Schema::dropIfExists('terms');
    }
};