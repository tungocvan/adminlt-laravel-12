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
        Schema::create('user_options', function (Blueprint $table) {
            $table->bigIncrements('option_id');

            // Gắn với user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Tên option (vd: theme, notification_setting,...)
            $table->string('option_name', 191);

            // Giá trị (có thể JSON, text dài,...)
            $table->longText('option_value')->nullable();

            // autoload: yes/no (nếu cần load tự động khi user login)
            $table->string('autoload', 20)->default('yes');

            // Mỗi user chỉ có một option_name duy nhất
            $table->unique(['user_id', 'option_name']);

            $table->index('autoload');

            $table->timestamps();
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
