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
        Schema::create('contact_sellers', function (Blueprint $table) {
            $table->id();
            $table->string('user_type')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');            
            $table->text('files')->nullable(); // lưu JSON array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_sellers');
    }
};
