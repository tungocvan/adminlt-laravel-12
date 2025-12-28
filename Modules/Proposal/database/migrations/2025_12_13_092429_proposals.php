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
        Schema::create('proposals', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->date('expected_time')->nullable();
        $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH'])->default('MEDIUM');
        $table->enum('status', ['PENDING'])->default('PENDING');
        $table->foreignId('created_by')->constrained('users');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
