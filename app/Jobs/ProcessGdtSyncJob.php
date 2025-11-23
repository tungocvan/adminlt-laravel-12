<?php

namespace App\Jobs;

use App\Services\GdtInvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class ProcessGdtInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startDate;
    public $endDate;
    public $vatIn;
    public $logKey;

    public function __construct($startDate, $endDate, $vatIn, $logKey)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->vatIn = $vatIn;
        $this->logKey = $logKey;
    }

    private function log($msg)
    {
        Redis::rpush($this->logKey, "[".now()->format('H:i:s')."] $msg");
    }

    public function handle(GdtInvoiceService $service)
    {
        $this->log('Bắt đầu xử lý hóa đơn...');

        $service->processRange(
            $this->startDate,
            $this->endDate,
            function($msg) {
                $this->log($msg);
            },
            $this->vatIn
        );

        $this->log('Hoàn tất xử lý!');
    }
}
