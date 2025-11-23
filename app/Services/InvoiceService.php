<?php

namespace App\Services;

use Modules\Invoices\Models\Invoices;
use Illuminate\Database\Eloquent\Builder;

class InvoiceService
{
    /**
     * Lấy danh sách hóa đơn theo filter tổng quát.
     *
     * @param array $filters
     *  'lookup_code' => string
     *  'symbol' => string
     *  'invoice_number' => string
     *  'type' => string
     *  'issued_date_from' => date
     *  'issued_date_to' => date
     *  'buyer_tax_code' => string
     *  'buyer_name' => string
     *  'buyer_email' => string
     *  'seller_name' => string
     *  'invoice_type' => 'sold'|'purchase'
     *
     * @param bool $getQueryBuilder nếu true trả về query builder, false trả về collection
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection
     */
    public function filter(array $filters = [], bool $getQueryBuilder = false)
    {
        $query = Invoices::query();

        // Lọc theo text
        foreach ([
            'lookup_code',
            'symbol',
            'invoice_number',
            'type',
            'issued_date',
            'tax_code',
            'name',        
            'address',
            'email',
            'phone',
            'tax_rate',
            'vat_amount',
            'amount_before_vat',
            'total_amount',
            'invoice_type'
        ] as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, 'like', '%' . $filters[$field] . '%');
            }
        }

          // Lọc theo invoice_type (sold | purchase)
          if (!empty($filters['invoice_type'])) {
            $query->whereRaw('LOWER(invoice_type) = ?', [strtolower($filters['invoice_type'])]);
        }


        // Lọc theo khoảng ngày
        if (!empty($filters['issued_date_from'])) {
            $query->where('issued_date', '>=', $filters['issued_date_from']);
        }

        if (!empty($filters['issued_date_to'])) {
            $query->where('issued_date', '<=', $filters['issued_date_to']);
        }
       

        // Trả về query builder hoặc collection
        return $getQueryBuilder ? $query : $query->get();
    }
}
