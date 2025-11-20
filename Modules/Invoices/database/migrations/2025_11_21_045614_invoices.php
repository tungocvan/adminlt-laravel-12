<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('lookup_code')->nullable();        // Mã tra cứu
            $table->string('symbol')->nullable();             // Ký hiệu
            $table->string('invoice_number')->nullable();     // Số hóa đơn
            $table->string('type')->nullable();               // Loại
            $table->date('issued_date')->nullable();          // Ngày lập

            $table->string('buyer_tax_code')->nullable();     // MST người mua
            $table->string('buyer_name')->nullable();         // Người mua
            $table->string('buyer_email')->nullable();        // Email người mua

            $table->string('seller_name')->nullable();        // Người bán

            $table->decimal('tax_rate', 5, 2)->nullable();             // Thuế suất (%)
            $table->decimal('vat_amount', 18, 2)->nullable();          // VAT
            $table->decimal('amount_before_vat', 18, 2)->nullable();   // Trước VAT
            $table->decimal('total_amount', 18, 2)->nullable();        // Thành tiền

            $table->enum('invoice_type', ['sold', 'purchase'])->default('sold'); // sold: hóa đơn đầu bán ra, purchase: hóa đơn mua vào
 


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
