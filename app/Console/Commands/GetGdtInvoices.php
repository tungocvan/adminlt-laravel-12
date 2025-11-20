<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class GetGdtInvoices extends Command
{
    protected $signature = 'gdt:invoices 
                            {from : Từ ngày (d/m/Y hoặc Y-m-d)} 
                            {to : Đến ngày (d/m/Y hoặc Y-m-d)} 
                            {--excel : Xuất Excel}';

    protected $description = 'Lấy hóa đơn GDT, phân trang & chia thời gian ≤1 tháng, xuất Excel trực tiếp (streaming), RAM thấp';

    public function handle()
    {
        set_time_limit(0); // vô hạn thời gian chạy
        $startAll = microtime(true);
        $this->info("⏳ Bắt đầu xử lý...");

        // ===== Parse ngày linh hoạt =====
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d'];
        $tryParse = function ($input) use ($formats) {
            foreach ($formats as $f) {
                try {
                    $d = Carbon::createFromFormat($f, $input);
                    if ($d && $d->format($f) === $input) return $d;
                } catch (\Exception $e) {}
            }
            return Carbon::parse($input);
        };

        try {
            $fromDate = $tryParse($this->argument('from'));
            $toDate   = $tryParse($this->argument('to'));
        } catch (\Exception $e) {
            $this->error("❌ Sai định dạng ngày! Hãy nhập d/m/Y hoặc Y-m-d");
            return;
        }

        // ===== Lấy token =====
        $token = Cache::get('gdt_token');
        if (!$token) {
            $this->error("❌ Chưa có token GDT. Hãy login trước!");
            return;
        }

        // ===== Tạo các khoảng thời gian ≤1 tháng =====
        $periods = [];
        $periodStart = $fromDate->copy();
        while ($periodStart <= $toDate) {
            $periodEnd = $periodStart->copy()->addMonth()->subDay();
            if ($periodEnd > $toDate) $periodEnd = $toDate->copy();
            $periods[] = [$periodStart->copy(), $periodEnd->copy()];
            $periodStart = $periodEnd->copy()->addDay();
        }

        $this->info("🔍 Bắt đầu gọi API theo từng khoảng ≤1 tháng...");

        // ===== Khởi tạo STT =====
        $counter = 1;

        // ===== Generator trực tiếp streaming Excel =====
        $generator = function() use ($periods, $token, &$counter) {
            foreach ($periods as [$pFrom, $pTo]) {
                $this->info("📅 Khoảng " . $pFrom->format('d/m/Y') . " → " . $pTo->format('d/m/Y'));
                $search = "tdlap=ge={$pFrom->format('d/m/Y')}T00:00:00;tdlap=le={$pTo->format('d/m/Y')}T23:59:59";

                $page = 1;
                $size = 50;

                while (true) {
                    $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/sold"
                        . "?sort=tdlap:desc,khmshdon:asc,shdon:desc&size={$size}&page={$page}&search={$search}";

                    $response = Http::withOptions(['verify'=>false])
                        ->withHeaders(['Authorization'=>"Bearer {$token}"])
                        ->get($url);

                    if (!$response->successful()) {
                        $msg = $response->json()['message'] ?? 'Không rõ lỗi';
                        $this->error("❌ Lấy hóa đơn thất bại: {$msg}");
                        return;
                    }

                    $data = $response->json();
                    $invoices = $data['datas'] ?? [];

                    if (empty($invoices)) break;

                    foreach ($invoices as $item) {
                        yield [
                            'STT' => $counter++,
                            'Mã tra cứu' => $item['cttkhac'][16]['dlieu'] ?? '',
                            'Ký hiệu' => ($item['khmshdon'] ?? '') . '/' . ($item['khhdon'] ?? ''),
                            'Số HĐ' => $item['shdon'] ?? '',
                            'Loại' => $item['thdon'] ?? '',
                            'Ngày lập' => isset($item['tdlap']) ? Carbon::parse($item['tdlap'])->format('d/m/Y') : '',
                            'MST Người mua' => $item['nmmst'] ?? '',
                            'Người mua' => $item['nmten'] ?? '',
                            'Email người mua' => $item['nmdctdtu'] ?? '',
                            'Người bán' => $item['nbten'] ?? '',
                            'Thuế suất' => $item['thttltsuat'][0]['tsuat'] ?? '',
                            'VAT' => $item['tgtthue'] ?? 0,
                            'Trước VAT' => $item['tgtcthue'] ?? 0,
                            'Thành tiền' => $item['tgtttbso'] ?? 0,
                        ];
                    }

                    if (count($invoices) < $size) break;
                    $page++;
                }
            }
        };

        // ===== Xuất Excel nếu có option =====
        if ($this->option('excel')) {
            $startExcel = microtime(true);
            $file = 'invoices-gdt-' . date('Ymd_His') . '.xlsx';
            (new FastExcel($generator()))->export(storage_path("app/{$file}"));
            $excelTime = microtime(true) - $startExcel;
            $this->info("📁 Excel đã lưu: storage/app/{$file}");
            $this->info("✔ Thời gian xuất Excel: " . number_format($excelTime, 3) . " giây");
        } else {
            // Nếu không xuất Excel, chỉ đếm số hóa đơn
            $total = iterator_count($generator());
            $this->info("✔ Tổng số hóa đơn: {$total}");
        }

        $totalTime = microtime(true) - $startAll;
        $this->info("⏲ Tổng thời gian thực thi: " . number_format($totalTime, 3) . " giây");
        $this->info("🎉 Hoàn thành!");
    }
}
