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
        Schema::create('options', function (Blueprint $table) {
            $table->bigIncrements('id');

            // polymorphic
            $table->morphs('optionable');
            // => Tạo optionable_id + optionable_type

            $table->string('option_name', 191);
            $table->json('option_value')->nullable();

            $table->string('autoload', 20)->default('no');

            // Unique per model
            $table->unique(['optionable_id', 'optionable_type', 'option_name']);

            // Index giúp query nhanh hơn
            $table->index('option_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
