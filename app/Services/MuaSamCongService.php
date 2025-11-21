<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MuaSamCongService
{
    private string $endpoint = 'https://muasamcong.mpi.gov.vn/o/egp-portal-personal-page/services/smart/search_prc';
    private string $cookie = 'COOKIE_SUPPORT=true; _ga=GA1.1.1190451015.1752638579; _gtpk_ses.6946.ea8d=1; _gtpk_testcookie..undefined=1; 5321a273c51a75133e0fb1cd75e32e27=6506b7b56d361f7cb4fd3eb2f0486eb8; df5f782085f475fb47cf8ea13597bc51=77d13e0cc2eb246243293347f6a9adee; GUEST_LANGUAGE_ID=vi_VN; LFR_SESSION_STATE_20103=1763692786692; NSC_WT_QSE_QPSUBM_NTD_NQJ=ffffffffaf183e2245525d5f4f58455e445a4a4217de; JSESSIONID=wEdv_avkNJD-ujM7-3eeVqtk-LS3lI0r-2DAChlI.dc_app1_02; _ga_19996Z37EE=GS2.1.s1763690184$o340$g1$t1763693538$j53$l0$h0; _gtpk_id.6946.ea8d=3965934490a5af0a.1752638577.330.1763693540.1763690183.; LFR_SESSION_STATE_19909668=1763693542436';

    /**
     * Trả về mảng dữ liệu, không JsonResponse
     */
    public function searchPricing(array $payload): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Referer'      => 'https://muasamcong.mpi.gov.vn/web/guest/profile-info?p_p_id=egpportalpersonalpage_WAR_egpportalpersonalpage&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&_egpportalpersonalpage_WAR_egpportalpersonalpage_render=personalUrl&menu=bid-pricing',
                'Origin'       => 'https://muasamcong.mpi.gov.vn',
                'Cookie'       => $this->cookie,
                'Accept'       => 'application/json, text/plain, */*',
                'User-Agent'   => request()->userAgent(),
            ])->post($this->endpoint, $payload);

            

            if ($response->failed()) {
                return [
                    'success' => false,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ];
            }
            
            return [
                'success' => true,
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function searchSmartV2(array $payload, string $token)
    {
        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/smart/search?token={$token}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Referer'      => 'https://muasamcong.mpi.gov.vn/',
            'Origin'       => 'https://muasamcong.mpi.gov.vn',
            'X-XSRF-TOKEN' => $this->xsrfToken,
            'Cookie'       => $this->cookie,
            'User-Agent'   => 'Mozilla/5.0',
            'Accept'       => 'application/json, text/plain, */*'
        ])->post($url, $payload);

        return [
            'status' => $response->status(),
            'data'   => $response->json(),
            'raw'    => $response->body()
        ];
    }


}
