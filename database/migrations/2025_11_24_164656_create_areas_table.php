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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // 1014311
            $table->string('area_type');               // areaType
            $table->string('name');                    // Xã Liên Phương
            $table->integer('order_index')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->date('created_date')->nullable();  // createdDate
            $table->string('created_by')->nullable();  // createdBy
            $table->string('parent_code')->nullable(); // parentCode
            $table->string('name_translate')->nullable();
            $table->timestamps();

            // Index
            $table->index('parent_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
