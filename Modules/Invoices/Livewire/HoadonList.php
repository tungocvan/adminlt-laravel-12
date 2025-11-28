<?php

namespace Modules\Invoices\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoices\Models\Invoices;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Invoices\Exports\InvoicesSelectedExport;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Http;

class HoadonList extends Component
{
    use WithPagination;
    public $downloadStatus = null;
    public $type = null;      // sold | purchase
    public $name = '';        // buyer hoặc seller, tùy type
    public $tax_code = '';
    public $from_date = '';
    public $to_date = '';
    public $taxRateFilter = 'all'; // all | 5% | 8% | 10% | other

    public $nameList = [];
    public $taxCodeList = [];
    public array $selected = [];
    public $perPage = 50; // mặc định
    public $token;

    protected $updatesQueryString = [
        'type', 'name', 'tax_code', 'from_date', 'to_date', 'taxRateFilter', 'page', 'perPage'
    ];

    public function mount()
    {
        $this->token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJBcHBJZCI6IjI4YTkzNTU1LWY5YmMtNDAyMi04ZjAzLTg2MDRjMmU5MmIyMSIsIkNvbXBhbnlJZCI6IjExNDI4NCIsIlJvbGVUeXBlIjoiMSIsIlVzZXJJZCI6ImZhYTNjOTQ1LTdlYjItNDI0Yi04NTczLTIwYWJlNzhkYzNiNSIsIlVzZXJOYW1lIjoiaW5hZm9zaXJvQGdtYWlsLmNvbSIsIk1pc2FJZCI6IjBjNWE0Y2Q1LTIxOGItNGM2ZS04ODBhLTAyZjMyNDZmYjc5OCIsIlBob25lTnVtYmVyIjoiIiwiRW1haWwiOiJpbmFmb3Npcm9AZ21haWwuY29tIiwiVGF4Q29kZSI6IjAzMTQ0OTIzNDUiLCJTZWN1cmVUb2tlbiI6IkdTZWZpZUhPR1FhR2hFSElnanNiNERJcW83NEs3UTEwVTllTThqMEFJY1pNMzFTcjdTUFp5UUJWUE1VT21QR1giLCJuYmYiOjE3NjM1NDI2OTksImV4cCI6MTc2NjEzNDY5OSwiaWF0IjoxNzYzNTQyNjk5LCJpc3MiOiJodHRwczovL21laW52b2ljZS52biIsImF1ZCI6Imh0dHBzOi8vbWVpbnZvaWNlLnZuIn0.eZHOBLfweN36gNssIQ-dY6CwXG5QEOxFDtTyBGd2-ok";
        // Ngày đầu năm hiện tại
        $this->from_date = Carbon::now()->startOfYear()->format('Y-m-d');
        // Ngày hôm nay
        $this->to_date = Carbon::now()->format('Y-m-d');
        $this->updateNameList();
        $this->updateTaxCodeList();
        
    }

    public function updatedType($value)
    {
        $this->resetFilters();
        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    public function updating($key)
    {
        if ($key !== 'page') $this->resetPage();
    }

    public function resetTomSelect($refName)
    {
        if($refName === 'nameSelect') {
            $this->tax_code = '';
        }
        if($refName === 'taxSelect') {
            $this->name = '';
        }
    }

    public function resetFilters()
    {
        $this->name = '';
        $this->tax_code = '';
        // $this->from_date = '';
        // $this->to_date = '';
        $this->taxRateFilter = 'all';

        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    private function updateNameList()
    {
        if (!$this->type) {
            $this->nameList = [];
            return;
        }

        $query = Invoices::query()
            ->where('invoice_type', $this->type)
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $this->nameList = $query->select('name')
            ->groupBy('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    private function updateTaxCodeList()
    {
        if (!$this->type) {
            $this->taxCodeList = [];
            return;
        }

        $query = Invoices::query()
            ->where('invoice_type', $this->type)
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $this->taxCodeList = $query->select('tax_code')
            ->groupBy('tax_code')
            ->orderBy('tax_code')
            ->pluck('tax_code')
            ->toArray();
    }

    public function getInvoicesProperty()
    {
        $query = Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->orderBy('issued_date', 'desc');

        if ($this->perPage === 'all') {
            return $query->get();
        }

        return $query->paginate($this->perPage);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }


    public function getFilteredTotalAmountProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=',$this->from_date))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=',$this->to_date))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->sum('total_amount');
    }

    public function getFilteredInvoiceCountProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->count();
    }

    public function getFilteredTotalByTaxRateProperty()
    {
        $query = Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $totals = [];

        foreach (['5%', '8%', '10%'] as $rate) {
            $totals[$rate] = (clone $query)->where('tax_rate', $rate)->sum('total_amount');
        }

        $totals['other'] = (clone $query)
            ->whereNotNull('tax_rate')
            ->whereNotIn('tax_rate', ['5%', '8%', '10%'])
            ->sum('total_amount');

        // $totals['empty'] = (clone $query)
        //     ->whereNull('tax_rate')
        //     ->sum('total_amount');

        return $totals;
    }

    public function getFilteredTotalVatProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->sum('vat_amount');
    }

    public function getYearlyRevenueByTypeProperty()
    {
        return Invoices::selectRaw(
                'YEAR(issued_date) as year,
                SUM(CASE WHEN invoice_type="sold" THEN total_amount ELSE 0 END) as sold_total,
                SUM(CASE WHEN invoice_type="purchase" THEN total_amount ELSE 0 END) as purchase_total'
            )
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get()
            ->toArray();
    }



    public function exportSelected()
    {
        if (count($this->selected) === 0) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn hóa đơn trước khi xuất!'
            ]);
            return;
        }

        return Excel::download(
            new InvoicesSelectedExport($this->selected),
            'hoadon_chon_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
   




  

    public function downloadSelected()
    {
        if (empty($this->selected)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn hóa đơn trước khi tải!'
            ]);
            return;
        }
        $this->downloadStatus = 'processing'; // Bắt đầu
        // 1️⃣ Lấy lookup_code từ database
        $lookupCodes = Invoices::whereIn('id', $this->selected)
            ->pluck('lookup_code')
            ->filter()
            ->toArray();
        
            $tempDir = storage_path('app/hoadon_temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
          //  dd($lookupCodes);
             // 1a️⃣ Lọc các lookup_code đã tồn tại file PDF trên server
            $lookupCodes = array_filter($lookupCodes, function($code) use ($tempDir) {
                $filePath = $tempDir . '/' . $code . '.pdf';
                return !file_exists($filePath);
            });
            $lookupCodes = array_values($lookupCodes);
            if (empty($lookupCodes)) {
                $this->dispatch('alert', [
                    'type' => 'info',
                    'message' => 'Các hóa đơn đã tồn tại trên server, không cần tải lại.'
                ]);
                return;
            }
       
        // 2️⃣ Gọi API MeInvoice để lấy link PDF
        
        $url = "https://api.meinvoice.vn/api/integration/invoice/publishview";

        $response = Http::withToken($this->token)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withBody(json_encode($lookupCodes), 'application/json')
            ->post($url);

        if (!$response->successful()) {
            $this->downloadStatus = 'error';
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'API lỗi: ' . $response->body()
            ]);
            return;
        }
        
        $downloadLink = $response->json('data');

        if (empty($downloadLink)) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'API không trả dữ liệu!'
            ]);
            return;
        }

       
        $originalPdf = $tempDir . '/hoadon_full_' . now()->format('Ymd_His') . '.pdf';
        file_put_contents($originalPdf, file_get_contents($downloadLink));

        // 4️⃣ Tách từng trang PDF và lưu trên server
        $pdfSource = new Fpdi();
        $pageCount = $pdfSource->setSourceFile($originalPdf);

        foreach ($lookupCodes as $index => $code) {
            $pdf = new Fpdi();
            $pdf->setSourceFile($originalPdf);
            $tplPage = $pdf->importPage($index + 1);
            $pdf->AddPage();
            $pdf->useTemplate($tplPage);

            $filePath = $tempDir . '/' . $code . '.pdf';
            $pdf->Output($filePath, 'F'); // F = save file trên server
        }

        // 5️⃣ Xóa file PDF full gốc
        if (file_exists($originalPdf)) {
            unlink($originalPdf);
        }

        // 5️⃣ Trả thông báo thành công
        $this->downloadStatus = 'success';
        $this->dispatch('download-success', [
            'type' => 'success',
            'message' => 'Đã tách các hóa đơn thành công, lưu tại: ' . $tempDir
        ]);
    }






 
    public function render()
    {
        $totalSoldAmount = Invoices::where('invoice_type', 'sold')->sum('total_amount');
        $totalPurchaseAmount = Invoices::where('invoice_type', 'purchase')->sum('total_amount');
        $totalSoldCustomers = Invoices::where('invoice_type', 'sold')->distinct('name')->count('name');
        $totalPurchaseCustomers = Invoices::where('invoice_type', 'purchase')->distinct('name')->count('name');

        return view('Invoices::livewire.hoadon-list', [
            'invoices' => $this->invoices,
            'totalSoldAmount' => $totalSoldAmount,
            'totalPurchaseAmount' => $totalPurchaseAmount,
            'totalSoldCustomers' => $totalSoldCustomers,
            'totalPurchaseCustomers' => $totalPurchaseCustomers,
            'yearlyRevenue' => $this->yearlyRevenueByType,
        ]);
    }
}
