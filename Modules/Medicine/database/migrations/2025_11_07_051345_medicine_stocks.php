<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicine_stocks', function (Blueprint $table) {
            $table->id(); // primary key auto-increment
            $table->unsignedBigInteger('medicine_id');
            $table->string('so_lo', 50)->default('số lô chưa có');
            $table->timestamp('han_dung')->useCurrent();
            $table->integer('so_luong')->default(0);
            $table->decimal('gia_von', 10, 2)->nullable(); // tối đa 99.999.999,99
            $table->decimal('don_gia', 10, 2)->nullable();     // tối đa 99.999.999,99
            $table->enum('status', ['available', 'expired', 'reserved'])->default('available');
            $table->string('location', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique index để đảm bảo không trùng lô + hạn dùng cho cùng thuốc
            $table->unique(['medicine_id', 'so_lo', 'han_dung']);

            // FK đến bảng medicines
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
