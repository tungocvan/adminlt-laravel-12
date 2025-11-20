<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $fillable = [
        'lookup_code',
        'symbol',
        'invoice_number',
        'type',
        'issued_date',
        'buyer_tax_code',
        'buyer_name',
        'buyer_email',
        'seller_name',
        'tax_rate',
        'vat_amount',
        'amount_before_vat',
        'total_amount',
        'invoice_type', // sold | purchase
    ];

    protected $casts = [
        'issued_date' => 'date',
        'tax_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'amount_before_vat' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public static function headers()
    {
        return [
            'Mã tra cứu',
            'Ký hiệu',
            'Số HĐ',
            'Loại',
            'Ngày lập',
            'MST Người mua',
            'Người mua',
            'Email Người mua',
            'Người bán',
            'Thuế suất',
            'VAT',
            'Trước VAT',
            'Thành tiền',
            'Loại hóa đơn',
        ];
    }
}
