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
        Schema::create('proposal_files', function (Blueprint $table) {
        $table->id();
        $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
        $table->string('file_path');
        $table->string('file_name');
        $table->foreignId('uploaded_by')->constrained('users');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};
