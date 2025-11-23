<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use OTPHP\TOTP;
use Illuminate\Support\Facades\Storage;

class MscLoginCommand extends Command
{
    protected $signature = 'msc:login 
        {username?} 
        {password?} 
        {--secret= : Google Authenticator secret key}';

    protected $description = 'Login MuaSamCong + sinh OTP (nếu có) + lưu cookie';

    public function handle()
    {
        $username = $this->argument('username') ?? env('MSC_USER');
        $password = $this->argument('password') ?? env('MSC_PASS');
        $secret   = $this->option('secret') ?? env('MSC_TOTP_SECRET');

        if (!$username || !$password) {
            $this->error("❌ Chưa set username hoặc password");
            return;
        }

        $casUrl = "https://muasamcong.mpi.gov.vn/web/guest/profile-info?p_p_id=egpportalpersonalpage_WAR_egpportalpersonalpage
";
        $this->info("👉 GET trang CAS login...");

        $get = Http::withOptions(['verify' => false])->get($casUrl);
       // dd( $get);
        if (!$get->successful()) {
            $this->error("❌ Không truy cập được CAS login");
            return;
        }

        $html = $get->body();

        // Parse lt + execution nếu có
        preg_match('/name="lt" value="(.*?)"/', $html, $m1);
        preg_match('/name="execution" value="(.*?)"/', $html, $m2);

        $lt = $m1[1] ?? null;
        $execution = $m2[1] ?? null;

        if ($lt && $execution) {
            $this->info("✔ Lấy lt + execution thành công");
        } else {
            $this->warn("⚠️ Không thấy lt/execution, sẽ thử login trực tiếp");
        }

        // Sinh OTP nếu có secret
        $otp = $secret ? TOTP::create($secret)->now() : null;
        if ($otp) $this->info("🔑 OTP hiện tại: $otp");

        $this->info("👉 Gửi POST login...");

        $postData = [
            'username' => $username,
            'password' => $password,
            '_eventId' => 'submit',
        ];

        if ($lt) $postData['lt'] = $lt;
        if ($execution) $postData['execution'] = $execution;
        if ($otp) $postData['otp'] = $otp;

        $cookies = [];
foreach ($get->cookies()->toArray() as $cookie) {
    $cookies[$cookie['Name']] = $cookie['Value'];
}

$post = Http::withOptions(['verify' => false])
    ->withCookies($cookies, 'muasamcong.mpi.gov.vn')
    ->asForm()
    ->post($casUrl, $postData);


        if ($post->status() >= 300 && $post->status() < 400) {
            $this->info("✅ Login thành công, nhận redirect + cookies");
        } elseif (!$post->successful()) {
            $this->error("❌ Login thất bại");
            dd($post->body());
        }

        // Lưu cookie
        $cookies = $post->cookies()->toArray();
        Storage::disk('local')->put('msc_cookies.json', json_encode($cookies, JSON_PRETTY_PRINT));
        $this->info("🍪 Cookies đã lưu vào storage/app/msc_cookies.json");
    }
}
