<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class CreateOrderCommand extends Command
{
    protected $signature = 'order:create 
        {user_id} 
        {customer_id} 
        {--products=}'; // dạng JSON

    protected $description = 'Tạo đơn hàng qua command';

    public function handle(OrderService $service)
    {
        $products = json_decode($this->option('products'), true);

        $payload = [
            'user_id'      => $this->argument('user_id'),
            'customer_id'  => $this->argument('customer_id'),
            'email'        => 'example@example.com', // hoặc lấy từ User
            'order_detail' => $products
        ];

        try {
            $order = $service->createOrder($payload);

            $this->info("Tạo đơn thành công #{$order->id}");
        } catch (\Exception $e) {
            $this->error("Lỗi: " . $e->getMessage());
        }
    }
}
