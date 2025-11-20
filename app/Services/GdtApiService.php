<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GdtApiService
{
    protected $baseUrl = 'https://hoadondientu.gdt.gov.vn:30000';

    public function loadCaptcha()
    {
        
        $response = Http::withOptions([
            'verify' => false,
        ])->get($this->baseUrl . '/captcha');

       if ($response->successful()) {
            return $response->json();            
        }
        return null;
    }



    /**
     * Authenticate & lấy token
     */
    public function login($username, $password, $cvalue, $ckey,$time=1800)
    {
        
        $res = Http::withOptions([
            'verify' => false, // bỏ kiểm tra SSL
        ])->post($this->baseUrl . '/security-taxpayer/authenticate', [
            'username' => $username,
            'password' => $password,
            'ckey' => $ckey,
            'cvalue' => $cvalue,
        ]);


        if ($res->successful()) {
            // kiểm tra key token đúng chưa
            $token = $res->json('token') ?? ($res->json('accessToken') ?? null);

            if ($token) {
                // Cache::put('gdt_token', $token, $time);
                Cache::forever('gdt_token', $token);
                // \Log::info('GDT Token saved to cache', ['token' => $token]);
            } 

            return [
                'status' => 'success',
                'token'  =>  $token
            ];
        }

        return [
                'status' => 'error',
                'token'  =>  null
            ];
    }
}
