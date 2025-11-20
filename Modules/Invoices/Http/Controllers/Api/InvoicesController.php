<?php

namespace Modules\Invoices\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\InvoiceService;


class InvoicesController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    public function index()
    {
        return response()->json([
            'status' => 'Api Invoices success',            
        ]);
    }  
    public function filter(Request $request)
    {
        \Log::info('Request All', $request->all());
        $filters = $request->only([
            'lookup_code',
            'symbol',
            'invoice_number',
            'type',
            'issued_date_from',
            'issued_date_to',
            'buyer_tax_code',
            'buyer_name',
            'buyer_email',
            'seller_name',
            'invoice_type',
            'tax_rate_min',
            'tax_rate_max',
            'vat_amount_min',
            'vat_amount_max',
            'amount_before_vat_min',
            'amount_before_vat_max',
            'total_amount_min',
            'total_amount_max',
        ]);

        $sort_by = $request->input('sort_by', 'issued_date'); // default sort
        $sort_order = $request->input('sort_order', 'desc');  // default desc

        $per_page = (int) $request->input('per_page', 20);

        // Lấy query builder từ service
        $query = $this->invoiceService->filter($filters, true);

        // Filter theo range số/decimal
        $decimalFields = [
            'tax_rate', 'vat_amount', 'amount_before_vat', 'total_amount'
        ];

        foreach ($decimalFields as $field) {
            if (isset($filters["{$field}_min"])) {
                $query->where($field, '>=', $filters["{$field}_min"]);
            }
            if (isset($filters["{$field}_max"])) {
                $query->where($field, '<=', $filters["{$field}_max"]);
            }
        }
        // Clone query để sum trước khi phân trang
        $statQuery = clone $query;
        $totalSum = $statQuery->sum('total_amount');
        $minIssued_date = $statQuery->min('issued_date');
        $maxIssued_date = $statQuery->max('issued_date');
        // Sắp xếp
        $query->orderBy($sort_by, $sort_order);

        // Phân trang
        $data = $query->paginate($per_page)->withQueryString();
        \Log::info('Filters', $filters);

        \Log::info($query->toSql(), $query->getBindings());

        return response()->json([
            'success' => true,
            'meta' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'sum_total_amount' => $totalSum,
                'issued_date_min' => $minIssued_date,
                'issued_date_max' => $maxIssued_date,
            ],
            'data' => $data->items(),
        ]);
    }

}
