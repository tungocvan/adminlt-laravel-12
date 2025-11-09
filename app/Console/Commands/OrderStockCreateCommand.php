<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderStockService;
use Exception;

class OrderStockCreateCommand extends Command
{
    protected $signature = 'orderstock:create {--stock=}';
    protected $description = 'Tạo hoặc cập nhật tồn kho sản phẩm từ JSON';

    public function handle(OrderStockService $service)
    {
        $stockOption = $this->option('stock');

        if (!$stockOption) {
            $this->error("Bạn phải truyền --stock='[...]'");
            return 1;
        }

        try {
            $stocks = json_decode($stockOption, true);
            if (!is_array($stocks)) {
                throw new Exception("JSON không hợp lệ");
            }

            foreach ($stocks as $item) {
                if (!isset($item['medicine_id']) || !isset($item['so_luong'])) {
                    $this->warn("Bỏ qua sản phẩm thiếu medicine_id hoặc so_luong");
                    continue;
                }

                $result = $service->addOrUpdateStock($item);
                $this->info("OK: medicine_id {$result->medicine_id}, so_luong {$result->so_luong}, so_lo {$result->so_lo}, han_dung {$result->han_dung}");
            }

            return 0;

        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
