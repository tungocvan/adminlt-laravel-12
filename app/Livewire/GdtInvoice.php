<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GdtInvoice extends Component
{
    public $username;
    public $password;
    public $ckey;
    public $cvalue;
    public $fromDate;
    public $toDate;

    public $token;
    public $invoices = [];
    public $total = 0;

    public function mount()
    {
        // Nếu token đã lưu trong cache, dùng luôn
        $this->token = Cache::get('gdt_token');
    }

    public function login()
    {
        if ($this->token) {
            return; // đã có token
        }

        $response = Http::withOptions([
            'verify' => false,
        ])->post('https://hoadondientu.gdt.gov.vn:30000/security-taxpayer/authenticate', [
            'username' => $this->username,
            'password' => $this->password,
            'ckey' => $this->ckey,
            'cvalue' => $this->cvalue,
        ]);
        
        if ($response->successful()) {
            $this->token = $response->json()['token'] ?? null;
            Cache::put('gdt_token', $this->token, 1800); // lưu 30 phút
        } else {
            session()->flash('error', $response->json()['message'] ?? 'Login failed');
        }
    }

    public function searchInvoices()
    {
       

        if (!$this->token) {
            session()->flash('error', 'Chưa có token. Vui lòng kiểm tra thông tin login.');
            return;
        }
        $from =  \Carbon\Carbon::parse($this->fromDate)->format('d/m/Y');
        $to   = \Carbon\Carbon::parse($this->toDate)->format('d/m/Y');
        $search = "tdlap=ge={$from}T00:00:00;tdlap=le={$to}T23:59:59";
        $url = "https://hoadondientu.gdt.gov.vn:30000/query/invoices/sold?sort=tdlap:desc,khmshdon:asc,shdon:desc&size=50&search={$search}";

       // dd($url);
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => "Bearer {$this->token}"
        ])->get($url);

        //dd($response->json());

        if ($response->successful()) {
            $data = $response->json();
            $this->invoices = $data['datas'] ?? [];
            $this->total = $data['total'] ?? 0;
        } else {
            session()->flash('error', 'Lấy hóa đơn thất bại: ' . ($response->json()['message'] ?? ''));
        }
        
    }   

    public function render()
    {
          // xem key chính xác

        return view('livewire.gdt-invoice');
    }
}
