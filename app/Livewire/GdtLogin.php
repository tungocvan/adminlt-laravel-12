<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\GdtApiService;

class GdtLogin extends Component
{
    public $username = '0314492345';
    public $password = 'Inafo2025@';
    public $captchaSvg = ''; // gán mặc định rỗng


    public $ckey;
    public $cvalue;
    public $token = null;

    public function mount()
    {
        // Kiểm tra token trong cache trước
         if ($cachedToken = Cache::get('gdt_token')) {
            $this->token = $cachedToken;
            return; // Token tồn tại, không cần login nữa
        }
        $gdt = new GdtApiService;        
        $captcha = $gdt->loadCaptcha();
       
        if(count($captcha) > 0){
            $this->ckey = $captcha['key'];
            $this->captchaSvg = $captcha['content'];
        }
    }

    public function login(GdtApiService $gdt)
    {
        // Kiểm tra token trong cache trước
        if (!$this->token) {           
            $response = $gdt->login($this->username, $this->password, $this->cvalue, $this->ckey,36000);
            $this->token = $response['token'] ?? null;      
        }
         $this->redirect('/invoices');
    }
    public function deleteToken()
    {
        //dd('deleteToken');
        Cache::forget('gdt_token');
        $this->redirect('/invoices');
    }
    public function render()
    {
        return view('livewire.gdt-login');
    }
}
