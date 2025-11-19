<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GdtApiService
{
    protected $baseUrl = 'https://hoadondientu.gdt.gov.vn:30000/security-taxpayer';

    /**
     * Lấy captcha từ server GDT
     */
    public function getCaptcha()
    {
        $res = Http::withoutVerifying()
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->get($this->baseUrl . '/captcha');

        if ($res->successful()) {
            // API trả về array: captchaId + captchaImage (Base64)
            return $res->json();
        }

        return null;
    }

    /**
     * Authenticate & lấy token
     */
    public function login($username, $password, $cvalue, $ckey, $captcha, $captchaId)
    {
        $payload = [
            'username' => $username,
            'password' => $password,
            'cvalue' => $cvalue,
            'ckey' => $ckey,
            'captcha' => $captcha,
            'captchaId' => $captchaId,
        ];

        $res = Http::withoutVerifying()
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/authenticate', $payload);

        if ($res->successful()) {
            // kiểm tra key token đúng chưa
            $token = $res->json('token') ?? ($res->json('accessToken') ?? null);

            if ($token) {
                Cache::put('gdt_token', $token, 1800);
                \Log::info('GDT Token saved to cache', ['token' => $token]);
            } else {
                \Log::error('Token null, không lưu cache');
            }

            return $res->json();
        }

        return $res->json(); // trả về lỗi nếu có
    }
}
