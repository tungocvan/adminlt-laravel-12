<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GdtLogin extends Component
{
    public $username = '0314492345';
    public $password = 'Inafo2025@';
    public $captchaSvg = ''; // gán mặc định rỗng

    public $ckey;
    public $cvalue;
    public $token;

    public function mount()
    {
        
        $this->loadCaptcha();
    }

    public function loadCaptcha()
    {
        // Kiểm tra token trong cache trước
        if ($cachedToken = Cache::get('gdt_token')) {
            $this->token = $cachedToken;
            return; // Token tồn tại, không cần login nữa
        }
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://hoadondientu.gdt.gov.vn:30000/captcha');

        //dd($response->json());
        if ($response->successful()) {
            $data = $response->json();
            $this->ckey = $data['key'];
            $this->captchaSvg = $data['content'];
        }
    }

    public function login()
    {
        

        // Thực hiện login nếu chưa có token
        $response = Http::withOptions([
            'verify' => false, // bỏ kiểm tra SSL
        ])->post('https://hoadondientu.gdt.gov.vn:30000/security-taxpayer/authenticate', [
            'username' => $this->username,
            'password' => $this->password,
            'ckey' => $this->ckey,
            'cvalue' => $this->cvalue,
        ]);

        if ($response->successful()) {
            $this->token = $response->json()['token'] ?? $response->json()['accessToken'] ?? null;

            if ($this->token) {
                Cache::put('gdt_token', $this->token, 1800); // lưu token 30 phút
            } else {
                session()->flash('error', 'Login success nhưng token không có.');
                $this->loadCaptcha(); // load lại captcha
            }
        } else {
            session()->flash('error', $response->json()['message'] ?? 'Login thất bại');
            $this->loadCaptcha(); // load lại captcha nếu login thất bại
        }
    }
    public function render()
    {
        return view('livewire.gdt-login');
    }
}
