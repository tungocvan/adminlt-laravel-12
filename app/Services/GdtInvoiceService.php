<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\Invoices\Models\Invoices;

class GdtInvoiceService
{
    /**
     * Xử lý dữ liệu theo khoảng thời gian
     */
    public function processRange($startDate, $endDate, callable $cb = null, $vatIn)
    {
        $show = fn($m) => $cb ? $cb($m) : null;

        $show('[GDT] Bắt đầu processRange...');
        $vatIn = (bool) $vatIn;

        $show($vatIn ? '[GDT] Hóa đơn đầu vào' : '[GDT] Hóa đơn đầu ra');

        $token = Cache::get('gdt_token');
        if (!$token) return $show('[GDT] ❌ Không có token trong cache');

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        $show("[GDT] Khoảng thời gian: {$start->format('d/m/Y')} → {$end->format('d/m/Y')}");

        $all = [];

        while ($start->lte($end)) {
            $chunkStart = $start->copy()->startOfMonth();
            $chunkEnd   = min($start->copy()->endOfMonth(), $end);

            $show("[GDT] Gọi API tháng: {$chunkStart->format('d/m/Y')} → {$chunkEnd->format('d/m/Y')}");

            $invoices = $this->fetchInvoicesByMonth($token, $chunkStart, $chunkEnd, $show, $vatIn);

            $show('[GDT] Thu được ' . count($invoices) . ' hóa đơn tháng này');

            $all = array_merge($all, $invoices);
            $start->addMonth();
            $this->appendLog('[GDT] Thu được ' . count($invoices) . ' hóa đơn tháng này');
        }

        $show('[GDT] Tổng cộng: ' . count($all) . ' hóa đơn');

        $file = $this->exportExcel($all, $vatIn);

        $show('[GDT] File Excel tạo ra: ' . $file);

        return $file;
    }

    // Phương thức appendLog:
    private function appendLog($msg)
    {
        $logs = Cache::get('gdt_log', []);
        $logs[] = "[" . now()->format('H:i:s') . "] " . $msg;
        Cache::put('gdt_log', $logs, 3600);        
    }
    /**
     * Lấy hóa đơn theo từng tháng
     */
    private function fetchInvoicesByMonth($token, $from, $to, callable $show, $vatIn)
    {
        $action = $vatIn ? 'purchase' : 'sold';

        $search = "tdlap=ge={$from->format('d/m/Y')}T00:00:00;tdlap=le={$to->format('d/m/Y')}T23:59:59";
        $pageSize = 50;

        // Lấy tổng số
        $total = $this->getTotalInvoices($token, $action, $search);
        if ($total === 0) {
            $show("ℹ Không có hóa đơn tháng này.");
            return [];
        }

        $totalPages = ceil($total / $pageSize);
        $show("📄 Tổng: {$total}, Số trang: {$totalPages}");

        $result = [];
        $processed = 0;

        for ($page = 1; $page <= $totalPages; $page++) {
            $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/$action"
                 . "?sort=tdlap:desc&size=$pageSize&page=$page&search=$search";

            $show("📄 Gọi Page {$page}...");

            $res = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => "Bearer $token"])
                ->get($url);

            if (!$res->successful()) {
                $show("❌ API lỗi Page {$page}: " . json_encode($res->json()));
                break;
            }

            foreach ($res->json()['datas'] ?? [] as $item) {
                $result[] = $this->mapInvoice($item, $vatIn);
                $processed++;

                if ($processed % 50 == 0) $show("🔔 Đã xử lý {$processed} hóa đơn");
            }
        }

        if ($processed % 50 !== 0) $show("✅ Tổng xử lý: {$processed}");

        return $result;
    }

    /**
     * Lấy tổng hóa đơn nhanh
     */
    private function getTotalInvoices($token, $action, $search)
    {
        $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/$action?sort=tdlap:desc&size=1&page=1&search=$search";

        $res = Http::withOptions(['verify' => false])
            ->withHeaders(['Authorization' => "Bearer $token"])
            ->get($url);

        return $res->successful() ? ($res->json()['total'] ?? 0) : 0;
    }

    /**
     * Map hóa đơn về dạng Excel
     */
    private function mapInvoice($item, $vatIn)
    {
        $isIn = !$vatIn;

        return [
            'Mã tra cứu'         => $item['cttkhac'][16]['dlieu'] ?? '',
            'Ký hiệu'            => ($item['khmshdon'] ?? '') . '/' . ($item['khhdon'] ?? ''),
            'Số hóa đơn'         => $item['shdon'] ?? '',
            'Loại hóa đơn'       => $item['thdon'] ?? '',
            'Ngày lập'           => isset($item['tdlap']) ? Carbon::parse($item['tdlap'])->format('d/m/Y') : '',

            'Mã số thuế'         => $isIn ? $item['nmmst'] : $item['nbmst'],
            'Đơn vị'             => $isIn ? $item['nmten'] : $item['nbten'],
            'Địa chỉ'            => $isIn ? $item['nmdchi'] : $item['nbdchi'],
            'Email'              => $isIn ? $item['nmdctdtu'] : $item['nbdctdtu'],
            'Phone'              => $isIn ? $item['nmsdthoai'] : $item['nbsdthoai'],

            'Thuế suất'          => $item['thttltsuat'][0]['tsuat'] ?? '',
            'Tiền VAT'           => $item['tgtthue'] ?? 0,
            'Trước VAT'          => $item['tgtcthue'] ?? 0,
            'Thành tiền'         => $item['tgtttbso'] ?? 0,
        ];
    }

    /**
     * Xuất Excel
     */
    private function exportExcel(array $data, bool $vatIn)
    {
        $folder = $vatIn
            ? storage_path('app/gdt/vat_in')
            : storage_path('app/gdt/vat_out');

        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $file = $folder . '/' . ($vatIn ? 'vat_in_' : 'vat_out_') . date('Ymd_His') . '.xlsx';

        (new FastExcel($data))->export($file);

        return $file;
    }

    /**
     * Import Excel vào DB
     */
    public function importExcel(string $filePath, string $invoiceType = 'sold', callable $cb = null)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File không tồn tại: $filePath");
        }

        $cb? $cb("📂 Import file: $filePath") : null;

        $rows = (new FastExcel())->import($filePath);
        $count = 0;

        foreach ($rows as $row) {
            Invoices::create($this->mapImportRow($row, $invoiceType));
            $count++;

            if ($cb && $count % 50 === 0) $cb("🔄 Imported: $count");
        }

        $cb? $cb("✅ Import xong: $count") : null;

        return $count;
    }

    private function mapImportRow($row, $invoiceType)
    {
        $issuedDate = $this->safeDate($row['Ngày lập'] ?? null);

        return [
            'lookup_code'     => $row['Mã tra cứu'] ?? null,
            'symbol'          => $row['Ký hiệu'] ?? null,
            'invoice_number'  => $row['Số hóa đơn'] ?? null,
            'type'            => $row['Loại hóa đơn'] ?? null,
            'issued_date'     => $issuedDate,

            'buyer_tax_code'  => $row['Mã số thuế'] ?? null,
            'buyer_name'      => $row['Đơn vị'] ?? null,
            'buyer_email'     => $row['Email'] ?? null,

            'tax_rate'        => $this->cleanDecimal($row['Thuế suất'] ?? 0),
            'amount_before_vat' => $this->cleanDecimal($row['Trước VAT'] ?? 0),
            'vat_amount'      => $this->cleanDecimal($row['Tiền VAT'] ?? 0),
            'total_amount'    => $this->cleanDecimal($row['Thành tiền'] ?? 0),

            'invoice_type'    => $invoiceType,
        ];
    }

    private function safeDate($value)
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function cleanDecimal($value)
    {
        $clean = preg_replace('/[^0-9.\-]/', '', str_replace(',', '', $value));
        return is_numeric($clean) ? floatval($clean) : 0;
    }
}
