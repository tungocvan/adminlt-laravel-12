<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GdtInvoiceService
{
    public function processRange($startDate, $endDate, callable $progressCallback = null,$vatIn)
    {
        // Helper hiển thị
        $show = function($msg) use ($progressCallback) {
            if ($progressCallback) {
                $progressCallback($msg); // hiện trên CLI
            } else {
                echo $msg . PHP_EOL; // fallback nếu không có callback
            }
        };

        $show("[GDT] BẮT ĐẦU processRange...");

        $token = Cache::get('gdt_token');
        if (!$token) {
            $show('[GDT] ❌ Không có token trong cache');
            return null;
        }

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        $show("[GDT] Khoảng thời gian: {$start->format('d/m/Y')} → {$end->format('d/m/Y')}");

        $allInvoices = [];

        while ($start->lte($end)) {

            $chunkStart = $start->copy()->startOfMonth();
            $chunkEnd   = $start->copy()->endOfMonth();
            if ($chunkEnd->gt($end)) $chunkEnd = $end;

            $show("[GDT] Gọi API tháng: {$chunkStart->format('d/m/Y')} → {$chunkEnd->format('d/m/Y')}");

            $invoices = $this->fetchInvoicesByMonth(
                $token,
                $chunkStart,
                $chunkEnd,
                function($msg) use ($show) {
                    $show($msg); // hiện tiến độ từng 50 hóa đơn
                },
                $vatIn
            );

            $show("[GDT] Thu được " . count($invoices) . " hóa đơn của tháng này");

            $allInvoices = array_merge($allInvoices, $invoices);

            $start->addMonth();
        }

        $show("[GDT] Tổng cộng: " . count($allInvoices) . " hóa đơn");

        $file = $this->exportExcel($allInvoices, $show,$vatIn); // truyền callback để exportExcel cũng hiển thị trên CLI

        $show("[GDT] File Excel sau khi export: " . ($file ?: 'NULL'));

        return $file;
    }



    private function fetchInvoicesByMonth($token, $from, $to, callable $progressCallback = null,$vatIn)
    {
        if($vatIn === true){
            $out="purchase";
        }else{
            $out="sold";
        }
        $results = [];
        $pageSize = 50;
        $processed = 0;

        $search = "tdlap=ge={$from->format('d/m/Y')}T00:00:00;tdlap=le={$to->format('d/m/Y')}T23:59:59";

        // Lấy tổng số hóa đơn trước để tính số page
        $urlTotal = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/$out?sort=tdlap:desc&size=1&page=1&search={$search}";
        $responseTotal = Http::withOptions(['verify' => false])
            ->withHeaders(['Authorization' => "Bearer $token"])
            ->get($urlTotal);

        if (!$responseTotal->successful()) {
            if ($progressCallback) {
                $progressCallback("❌ API lỗi khi lấy tổng hóa đơn: " . json_encode($responseTotal->json()));
            }
            return $results;
        }

        $total = $responseTotal->json()['total'] ?? 0;
        if ($total === 0) {
            if ($progressCallback) {
                $progressCallback("ℹ Tháng này không có hóa đơn.");
            }
            return $results;
        }

        $totalPages = ceil($total / $pageSize);
        if ($progressCallback) {
            $progressCallback("📄 Tổng hóa đơn: {$total}, chia ra {$totalPages} page(s).");
        }

        // Bắt đầu loop theo page
        for ($page = 1; $page <= $totalPages; $page++) {
            $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/$out?sort=tdlap:desc&size={$pageSize}&page={$page}&search={$search}";
            if ($progressCallback) {
                $progressCallback("📄 Gọi Page {$page}...");
            }

            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => "Bearer $token"])
                ->get($url);

            if (!$response->successful()) {
                if ($progressCallback) {
                    $progressCallback("❌ API lỗi Page {$page}: " . json_encode($response->json()));
                }
                break;
            }

            $items = $response->json()['datas'] ?? [];

            foreach ($items as $item) {
                $results[] = $this->mapInvoice($item);
                $processed++;

                if ($progressCallback && $processed % 50 === 0) {
                    $progressCallback("🔔 Đã xử lý {$processed} hóa đơn...");
                }
            }
        }

        // Hiển thị tổng số hóa đơn nếu chưa chia hết 50
        if ($progressCallback && $processed % 50 !== 0) {
            $progressCallback("✅ Tổng số hóa đơn xử lý: {$processed}");
        }

        return $results;
    }




    private function mapInvoice($item)
    {
        return [
            'Mã tra cứu hóa đơn' => $item['cttkhac'][16]['dlieu'] ?? '',
            'Ký hiệu hóa đơn'    => ($item['khmshdon'] ?? '') . '/' . ($item['khhdon'] ?? ''),
            'Số hóa đơn'         => $item['shdon'] ?? '',
            'Loại hóa đơn'       => $item['thdon'] ?? '',
            'Ngày lập'           => isset($item['tdlap']) ? Carbon::parse($item['tdlap'])->format('d/m/Y') : '',
            'MST Người mua'      => $item['nmmst'] ?? '',
            'Người mua'          => $item['nmten'] ?? '',
            'Email người mua'    => $item['nmdctdtu'] ?? '',
            'Người bán'          => $item['nbten'] ?? '',
            'Thuế suất'          => $item['thttltsuat'][0]['tsuat'] ?? '',
            'Tiền VAT'           => $item['tgtthue'] ?? 0,
            'Tiền trước VAT'     => $item['tgtcthue'] ?? 0,
            'Thành tiền'         => $item['tgtttbso'] ?? 0,
        ];
    }

    private function exportExcel($data,$show,$vatIn)
    {
        if($vatIn === true){
            $folder = storage_path('app/gdt/vat_in');
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $filePath = $folder . '/inafo_vat_in_' . date('Ymd_His') . '.xlsx';
        }else{
            
            $folder = storage_path('app/gdt/vat_out');
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $filePath = $folder . '/inafo_vat_out_' . date('Ymd_His') . '.xlsx';
        }
        

        //Log::info("[GDT] Bắt đầu xuất Excel: {$filePath}");

        (new FastExcel($data))->export($filePath);

        //Log::info("[GDT] Xuất Excel thành công: {$filePath}");

        return $filePath;
    }
}
