<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Order\Models\Order;
use App\Models\MedicineStock;

class UpdateOrderCommand extends Command
{
    protected $signature = 'order:update 
        {order_id : ID của order cần sửa}
        {--data= : JSON dữ liệu update}';

    protected $description = 'Cập nhật order và tự động lấy số lô + hạn dùng từ MedicineStock';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $jsonData = $this->option('data');

        if (!$jsonData) {
            $this->error("❌ Bạn phải truyền option --data với JSON hợp lệ.");
            return Command::FAILURE;
        }

        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ JSON không hợp lệ: " . json_last_error_msg());
            return Command::FAILURE;
        }

        $order = Order::find($orderId);
        if (!$order) {
            $this->error("❌ Order không tồn tại!");
            return Command::FAILURE;
        }

        // ✅ update các field cơ bản
        $fields = ['customer_id', 'status', 'order_note'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $order->$field = $data[$field];
            }
        }

        // ✅ xử lý order_detail
        if (isset($data['order_detail'])) {

            $result = [];

            foreach ($data['order_detail'] as $item) {

                if (!isset($item['product_id'])) {
                    $this->error("❌ Mỗi order_detail phải có product_id");
                    return Command::FAILURE;
                }

                $quantity = $item['quantity'] ?? 1;

                // ✅ Tự động lấy số lô + hạn dùng theo product_id
                $stock = MedicineStock::where('medicine_id', $item['product_id'])
                    ->orderBy('han_dung', 'asc')
                    ->first();

                if (!$stock) {
                    $this->error("❌ Không tìm thấy hàng tồn cho product_id: {$item['product_id']}");
                    return Command::FAILURE;
                }

                $result[] = [
                    'product_id' => $item['product_id'],
                    'quantity'   => $quantity,
                    'so_lo'      => $stock->so_lo,
                    'han_dung'   => $stock->han_dung,
                ];
            }

            // Lưu JSON đã hoàn chỉnh
            $order->order_detail = json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        $order->save();

        $this->info("✅ Order #{$order->id} cập nhật thành công và đã tự lấy số lô + hạn dùng!");

        return Command::SUCCESS;
    }
}
