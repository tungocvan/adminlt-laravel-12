<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMscApi extends Command
{
    protected $signature = 'msc:test                             
                            {--payload= : File JSON payload}';

    protected $description = 'Test API MuaSamCong search_prc';
    // php artisan msc:test --cookie="JSESSIONID=xxxx; LFR_SESSION_STATE_20103=..." --payload=payload.json

    public function handle()
    {
        $cookie = 'COOKIE_SUPPORT=true; _ga=GA1.1.1190451015.1752638579; _gtpk_ses.6946.ea8d=1; _gtpk_testcookie..undefined=1; 5321a273c51a75133e0fb1cd75e32e27=6506b7b56d361f7cb4fd3eb2f0486eb8; df5f782085f475fb47cf8ea13597bc51=77d13e0cc2eb246243293347f6a9adee; GUEST_LANGUAGE_ID=vi_VN; LFR_SESSION_STATE_20103=1763692786692; NSC_WT_QSE_QPSUBM_NTD_NQJ=ffffffffaf183e2245525d5f4f58455e445a4a4217de; JSESSIONID=wEdv_avkNJD-ujM7-3eeVqtk-LS3lI0r-2DAChlI.dc_app1_02; _ga_19996Z37EE=GS2.1.s1763690184$o340$g1$t1763693538$j53$l0$h0; _gtpk_id.6946.ea8d=3965934490a5af0a.1752638577.330.1763693540.1763690183.; LFR_SESSION_STATE_19909668=1763693542436';
        $payloadFile = $this->option('payload');

       

        if (!$payloadFile || !file_exists($payloadFile)) {
            $this->error("Bạn cần truyền --payload=/path/to/payload.json");
            return 1;
        }

        $payload = json_decode(file_get_contents($payloadFile), true);

        if (!$payload) {
            $this->error("File payload không hợp lệ hoặc không đọc được JSON");
            return 1;
        }

        $endpoint = 'https://muasamcong.mpi.gov.vn/o/egp-portal-personal-page/services/smart/search_prc';

        $response = Http::withHeaders([
            'Accept'       => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'Origin'       => 'https://muasamcong.mpi.gov.vn',
            'Referer'      => 'https://muasamcong.mpi.gov.vn/web/guest/profile-info?p_p_id=egpportalpersonalpage_WAR_egpportalpersonalpage&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&_egpportalpersonalpage_WAR_egpportalpersonalpage_render=personalUrl&menu=bid-pricing',
            'Cookie'       => $cookie,
            'User-Agent'   => 'Mozilla/5.0',
        ])->post($endpoint, $payload);

        if ($response->failed()) {
            $this->error("Request failed: ".$response->status());
            $this->line($response->body());
            return 1;
        }

        $this->info("Request thành công:");
        $this->line(json_encode($response->json(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return 0;
    }
}
