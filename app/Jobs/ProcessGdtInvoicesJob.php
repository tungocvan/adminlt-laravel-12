<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GdtInvoiceService;
use Illuminate\Support\Facades\Log;


class ProcessGdtInvoicesJob implements ShouldQueue
{
use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


public $start;
public $end;


public function __construct($start, $end)
{
$this->start = $start;
$this->end = $end;
}


public function handle()
{
Log::info("[GDT JOB] Bắt đầu xử lý từ {$this->start} đến {$this->end}");


$service = new GdtInvoiceService();
$service->processRange($this->start, $this->end);


Log::info("[GDT JOB] Hoàn tất xử lý!");
}
}