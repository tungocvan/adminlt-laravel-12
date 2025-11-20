<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GdtInvoiceService
{
    public function processRange($startDate, $endDate)
    {
        $token = Cache::get('gdt_token');
        if (!$token) {
            Log::error('[GDT] Không có token');
            return;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $allInvoices = [];

        while ($start->lte($end)) {
            $chunkStart = $start->copy()->startOfMonth();
            $chunkEnd = $start->copy()->endOfMonth();

            if ($chunkEnd->gt($end)) {
                $chunkEnd = $end;
            }

            Log::info("[GDT] Gọi API tháng: {$chunkStart->format('d/m/Y')} - {$chunkEnd->format('d/m/Y')}");

            $invoices = $this->fetchInvoicesByMonth($token, $chunkStart, $chunkEnd);
            $allInvoices = array_merge($allInvoices, $invoices);

            $start->addMonth();
        }

        $this->exportExcel($allInvoices);
    }
    private function fetchInvoicesByMonth($token, $from, $to)
    {
        $results = [];
        $page = 1;

        do {
            $search = "tdlap=ge={$from->format('d/m/Y')}T00:00:00;tdlap=le={$to->format('d/m/Y')}T23:59:59";
            $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/sold?sort=tdlap:desc&size=50&page={$page}&search={$search}";

            Log::info("[GDT] Page {$page}");

            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => "Bearer $token"])
                ->get($url);

            if (!$response->successful()) {
                Log::error('[GDT] API lỗi: ' . json_encode($response->json()));
                break;
            }

            $data = $response->json();
            $items = $data['datas'] ?? [];

            foreach ($items as $item) {
                $results[] = $this->mapInvoice($item);
            }

            $page++;
        } while (count($items) === 50);

        return $results;
    }
    private function mapInvoice($item)
    {
        return [
            'Mã tra cứu hóa đơn' => $item['cttkhac'][16]['dlieu'] ?? '',
            'Ký hiệu hóa đơn' => ($item['khmshdon'] ?? '') . '/' . ($item['khhdon'] ?? ''),
            'Số hóa đơn' => $item['shdon'] ?? '',
            'Loại hóa đơn' => $item['thdon'] ?? '',
            'Ngày lập' => isset($item['tdlap']) ? Carbon::parse($item['tdlap'])->format('d/m/Y') : '',
            'MST Người mua' => $item['nmmst'] ?? '',
            'Người mua' => $item['nmten'] ?? '',
            'Email người mua' => $item['nmdctdtu'] ?? '',
            'Người bán' => $item['nbten'] ?? '',
            'Thuế suất' => $item['thttltsuat'][0]['tsuat'] ?? '',
            'Tiền VAT' => $item['tgtthue'] ?? 0,
            'Tiền trước VAT' => $item['tgtcthue'] ?? 0,
            'Thành tiền' => $item['tgtttbso'] ?? 0,
        ];
    }

    private function exportExcel($data)
    {
        $path = storage_path('app/gdt');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = $path . '/invoices_' . date('Ymd_His') . '.xlsx';

        Log::info("[GDT] Xuất Excel: {$file}");

        new FastExcel($data)->export($file);

        Log::info('[GDT] Xuất Excel hoàn tất');
    }
}
