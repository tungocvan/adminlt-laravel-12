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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('email');            
            $table->unsignedBigInteger('user_id');
            $table->json('order_detail'); // chứa mảng sản phẩm dạng JSON
            $table->decimal('total', 12, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'finished'])->default('pending');
            $table->text('order_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('link_download')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
