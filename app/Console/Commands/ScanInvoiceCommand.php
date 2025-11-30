<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Invoices\Services\ScanInvoiceService;
use Modules\Invoices\Services\InvoiceExportService;

class ScanInvoiceCommand extends Command
{
    protected $signature = 'invoice:scan {file?}';
    protected $description = 'Quét & parse hóa đơn điện tử PDF';

    public function handle()
    {
        $file = $this->argument('file') ;

        try {
            $scanService = new ScanInvoiceService();
            $exportService = new InvoiceExportService();

            $allItems = []; // chứa tất cả items của mọi file

            if ($file) {
                // ===== XỬ LÝ 1 FILE =====
                $this->info("Đang xử lý: " . $file);

                $result = $scanService->scan($file);
                dd($result);
                foreach ($result['items'] as $item) {
                    $item['source_file'] = basename($file);
                    $item['buyer'] = $result['buyer'];
                    $item['invoice'] = $result['invoice'];
                    $allItems[] = $item;
                }
            } else {
                // ===== XỬ LÝ TOÀN BỘ THƯ MỤC =====
                $folder = storage_path('app/hoadon_temp');
                $files = glob($folder . '/*.pdf');

                if (empty($files)) {
                    $this->warn("Không có file PDF trong thư mục.");
                    return;
                }

                foreach ($files as $pdf) {

                    $this->info("Đang xử lý: " . basename($pdf));

                    $result = $scanService->scan($pdf);

                    foreach ($result['items'] as $item) {
                        $item['source_file'] = basename($pdf);
                        $item['buyer'] = $result['buyer'];
                        $item['invoice'] = $result['invoice'];
                        $allItems[] = $item;
                    }
                }
            }

            // ===== XUẤT FILE DUY NHẤT =====
            $output = storage_path('app/all_items.xlsx');
            $exportService->exportItemsToExcelMerged($allItems, $output);

            $this->info("Đã xuất Excel: " . $output);

        } catch (\Exception $e) {
            $this->error("Lỗi: " . $e->getMessage());
        }
    }

}
