<?php

namespace Modules\Invoices\Livewire;

use Livewire\Component;
use App\Services\GdtInvoiceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
class SearchHoadon extends Component
{
    public $start_date;
    public $end_date;
    public $vatIn = false;     // false = bán ra, true = mua vào
    public $useQueue = false;  // xử lý qua queue hay không
    public $logs = [];

    protected $listeners = ['pollLogs'];

    public function mount()
    {
        Cache::forget('gdt_log'); // reset log trước khi chạy
        // mặc định lấy tháng hiện tại
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date   = now()->format('Y-m-d');
    }

    

    private function log($msg)
    {
        $this->logs[] = "[" . now()->format("H:i:s") . "] " . $msg;
        $this->dispatch('scroll-bottom');
    }

    public function run()
    {

        
        $this->logs = [];
        $this->log("Bắt đầu xử lý…");

        $start = Carbon::parse($this->start_date)->format('d/m/Y');
        $end   = Carbon::parse($this->end_date)->format('d/m/Y');

        if ($this->useQueue) {

            // gọi command đưa job vào queue
            Artisan::call("gdt:invoices $start $end --queue" . ($this->vatIn ? " --vatIn" : ""));

            $this->log("Đã đưa vào queue thành công!");
            return;
        }

        // Chạy trực tiếp – không queue
        $service = new GdtInvoiceService();

        $service->processRange(
            $this->start_date,
            $this->end_date,
            function($msg) {                
                $this->log($msg);
                //$this->dispatch('scroll-bottom');   
            },
            $this->vatIn
        ); 
        $this->log("Hoàn tất xử lý!");
        $token = Cache::get('gdt_token');
        if (!$token){
            session()->flash('status', 'Token đã hết hạn.');
            return $this->redirect('/invoices/create-token');
        }


    }

    public function pollLogs()
    {
        $this->logs = Cache::get('gdt_log', []);
        
    }

    public function importExcel()
    {
        $this->logs = [];

        $this->log("Bắt đầu import Excel…");

        $type = $this->vatIn ? 'purchase' : 'sold';

        $filePath = $this->vatIn 
            ? storage_path("app/gdt/vat_in/vat_in_{$this->start_date}_{$this->end_date}.xlsx")
            : storage_path("app/gdt/vat_out/vat_out_{$this->start_date}_{$this->end_date}.xlsx");

        if (!file_exists($filePath)) {
            $this->log("❌ File không tồn tại: $filePath");
            return;
        }

        $service = new \App\Services\InvoiceImportService();

        $service->import($filePath, $type, function($msg) {
            $this->log($msg);
        });

        $this->log("🎯 Import hoàn tất!");
    }


    public function render()
    {
        return view('Invoices::livewire.search-hoadon');
    }
}
