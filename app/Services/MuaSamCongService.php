<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MuaSamCongService
{
    private string $endpoint = 'https://muasamcong.mpi.gov.vn/o/egp-portal-personal-page/services/smart/search_prc';
    private string $cookie = 'COOKIE_SUPPORT=true; _ga=GA1.1.1190451015.1752638579; _gtpk_ses.6946.ea8d=1; _gtpk_testcookie..undefined=1; 5321a273c51a75133e0fb1cd75e32e27=6506b7b56d361f7cb4fd3eb2f0486eb8; df5f782085f475fb47cf8ea13597bc51=77d13e0cc2eb246243293347f6a9adee; GUEST_LANGUAGE_ID=vi_VN; LFR_SESSION_STATE_20103=1763692786692; NSC_WT_QSE_QPSUBM_NTD_NQJ=ffffffffaf183e2245525d5f4f58455e445a4a4217de; JSESSIONID=wEdv_avkNJD-ujM7-3eeVqtk-LS3lI0r-2DAChlI.dc_app1_02; _ga_19996Z37EE=GS2.1.s1763690184$o340$g1$t1763693538$j53$l0$h0; _gtpk_id.6946.ea8d=3965934490a5af0a.1752638577.330.1763693540.1763690183.; LFR_SESSION_STATE_19909668=1763693542436';

    private string $token ='0cAFcWeA5VH_7HfcQHWvpd14YdZc5tuqPnXwkbZaYnXXBcWe1iB1BaCEf_XmKf1Wg5ueUYq1Wc4A9dM4UxZWjvVqH8wQ30faLr5EtxvWVx1oQq9uX8C7VBJ6h6-fmlnyPR-eueUojhNMnZezPYDyyfoI1q09xD3hP3CGlWyO9j_ts72xhyXoWgC8LZvu8wQLJ3TLPvKR4nbqkRJ25oVrCLL5FUOK4f4eoUG7IbBcicwm_wf9jeb_HJ-Crc590lQYS-F_WM7rUEkdo7nWEbjGk4sLCOvEsagclEnmGAukxm9lUm8nXqGVO7EarJ6A8xzO2TWr2IzPHtQwengxVMCePS8DN7mAlkB8frs5_YE0s2Hp6v2rUCyBTRD5xxmjxp_wMXplw_XAkMEHUCJEAoUnPYvOtQPo4rkbEHIog5g7WmnWbCPpyBxBnuSEmBujwFanuV8oRtmstC-PRtTReVinR9lqkOYCvXdWSKx3p-g2fczoDICz7fdxmkR-fbhmwal6qCpx91n81RdO4nLUTRXhqceAGPy_vqkDz-faa_erGpLmfXpeoc12hx58fCkdgX4_npKGREFkv7nVWZMp4hMFXo-gl8ZiQnkVUz6uy8qf5XFveyjShxeZMoKjyvOGH_wMvx3yVE4eZVgS5z3auZZt-WjtuEBXkeWshKM8OxiWA4RGhcmMuii_6ayh4bqUKJG3Cq-gMKDtWBaz65lz5n6iCgcVU_wrjMQkiYJvRDhgusU_c0Y_pl_zPemnRDrXHMe5NQebpy4SinUq_Bq4xEGI8AEPqwkdQB6t9xAmhqoEDvocnJuFQZgOzBYeMGE04sEK9-GOndWUtJzLNnmSdt174NOA4V9IHqNZj1FbQHUel3Cyz_F0U-ZOnmGxx4bGF-v0X0u26uKhR9n-AWcYDcde7xUM1ejzVEDobStvS8ZKFfNNvjY9sf8SawSztRsHl1czY_-oU5IgjsV32dgOaiRAPcUS9GDcrYcGLuC8jB3B_Y0awbHlQc71b4PT-vfVIxKK9cjkqsQhbSCejXC0QGtZFHqW45WVs5Q9xl8TWAB8VHcv4R2JbVIDD_dykPVp6mtl-LVubwi6r1V7kGWFfAMgcgbMnWH8RCR8k_Cz6oGQk78uOMcGxoqJdOB5Lr0hGdPL15Oc4sPb54fN4MenDcO_QgNSvohRx6zJilSiDPrHy2j9XAQSt9uArtFHH9jnWoD8lOtoyQ-WcvsWd00IFJOppIeSF2a9-gEgaQ1PSD2Nj3S1lFfthVhbbNt5p9xPwFguwJAFZd2LAo9yTf02UHC1AMwJD0jnb4OTAfIT2fHrjmvzC--7xE1T3iC95zr6380uE7BaPgrgpi_-hhYinDT3WK8QeIJl2Pj0GC9mTETKGpEe9l41VzjcNhc8y7D-esO9av4BF8qXmhIsnS_9luB6j1i-MXR9PPd_Bv9iOPMTXdXU1aYfSwhnRGXEL1InZcI3d8_pamhBdxN4pKmYQVkwAjS9DEsBSGCdzHjiRTJ5CwvHaOh_2FYbYpWcEXQJqluhEYlxDwnOERyunS6VVFTepMMpnbN9ZtF041hCfMXQ_pfUF902gt-TzTIR26-llCezgMXgGhp8ucd5S7';
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

            //dd($response->json());

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

    public function searchSmartV2(array $payload)
    {
        $url = "https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/services/smart/search?token={$this->token}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Referer'      => 'https://muasamcong.mpi.gov.vn/',
            'Origin'       => 'https://muasamcong.mpi.gov.vn',         
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

    public function login(string $username, string $password)
    {
        // B1: Load trang login
        $page = Http::withOptions(['verify' => false])
            ->get('https://muasamcong.mpi.gov.vn/cas/login');

        $html = $page->body();
        $cookies = $page->cookies();

        // Lấy lt và execution từ HTML
        preg_match('/name="lt" value="([^"]+)"/', $html, $matchLt);
        preg_match('/name="execution" value="([^"]+)"/', $html, $matchEx);

        $lt = $matchLt[1] ?? null;
        $execution = $matchEx[1] ?? null;

        if (!$lt || !$execution) {
            return [
                'success' => false,
                'message' => 'Không lấy được lt/execution từ CAS login'
            ];
        }

        // B2: Gửi POST login
        $loginResponse = Http::withOptions(['verify' => false])
            ->withCookies($cookies->toArray(), 'muasamcong.mpi.gov.vn')
            ->asForm()
            ->post('https://muasamcong.mpi.gov.vn/cas/login', [
                'username'  => $username,
                'password'  => $password,
                'lt'        => $lt,
                'execution' => $execution,
                '_eventId'  => 'submit',
            ]);

        // Sau login hệ thống redirect 302 → thành công
        if ($loginResponse->status() !== 302) {
            return [
                'success' => false,
                'status'  => $loginResponse->status(),
                'body'    => $loginResponse->body()
            ];
        }

        // Hợp nhất cookies
        $mergedCookies = array_merge(
            $cookies->toArray(),
            $loginResponse->cookies()->toArray()
        );

        return [
            'success' => true,
            'cookies' => $mergedCookies
        ];
    }

    public function getSmartToken(array $cookies)
    {
        $resp = Http::withOptions(['verify' => false])
            ->withCookies($cookies, 'muasamcong.mpi.gov.vn')
            ->get('https://muasamcong.mpi.gov.vn/o/egp-portal-contractor-selection-v2/auth/token');

        return $resp->json()['token'] ?? null;
    }
}
