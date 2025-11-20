<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessGdtInvoicesJob;
use Carbon\Carbon;

class GdtInvoicesQueueCommand extends Command
{
    protected $signature = 'gdt:invoices:queue {start_date} {end_date}';

    protected $description = 'Đưa job lấy hóa đơn GDT vào queue để xử lý nền';

    private function parseDateFlexible($date)
    {
        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            $d = Carbon::createFromFormat($format, $date);
            if ($d !== false) {
                return $d;
            }
        }

        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function handle()
    {
        $startInput = $this->argument('start_date');
        $endInput = $this->argument('end_date');

        $start = $this->parseDateFlexible($startInput);
        $end = $this->parseDateFlexible($endInput);

        if (!$start || !$end) {
            $this->error('❌ Sai định dạng ngày! Hãy nhập dạng: d/m/Y hoặc Y-m-d');
            return Command::FAILURE;
        }

        $this->info("📌 Đưa job xử lý hóa đơn từ {$start->format('d/m/Y')} đến {$end->format('d/m/Y')} vào queue...");

        dispatch(new ProcessGdtInvoicesJob($start->toDateString(), $end->toDateString()));

        $this->info('✅ Đã đưa job vào queue thành công!');
        return Command::SUCCESS;
    }
}
