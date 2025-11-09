<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class DeleteOrderCommand extends Command
{
    protected $signature = 'order:delete {order_id}';
    protected $description = 'Xóa đơn hàng theo ID';

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle()
    {
        $orderId = $this->argument('order_id');

        $result = $this->orderService->delete($orderId);

        if (!$result['success']) {
            $this->error($result['message']);
            return Command::FAILURE;
        }

        $this->info("✅ " . $result['message']);
        return Command::SUCCESS;
    }
}
