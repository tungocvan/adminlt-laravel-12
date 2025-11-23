<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MuaSamCongService;
use Carbon\Carbon;

class TestHSMTCommand extends Command
{
    protected $signature = 'msc:test-hsmt 
        {keyword : Từ khóa gói thầu} 
        {range : Khoảng ngày dạng YYYY-MM-DD:YYYY-MM-DD}';

    protected $description = 'Test API tra cứu hồ sơ mời thầu (HSMT) trên muasamcong.mpi.gov.vn';

    public function handle(MuaSamCongService $service)
    {
        $keyword = $this->argument('keyword');
        $range   = $this->argument('range');

        // Tách range
        if (!str_contains($range, ':')) {
            $this->error("❌ Sai format range. VD: 2025-11-20:2025-11-21");
            return;
        }

        [$from, $to] = explode(':', $range);

        try {
            $fromIso = Carbon::parse($from)->startOfDay()->toISOString();
            $toIso   = Carbon::parse($to)->endOfDay()->toISOString();
        } catch (\Exception $e) {
            $this->error("❌ Không parse được ngày: " . $e->getMessage());
            return;
        }

        // Payload chuẩn theo yêu cầu
        $payload = [
            [
                "pageNumber" => "0",
                "query" => [
                    [
                        "index"        => "es-contractor-selection",
                        "keyWord"      => $keyword,
                        "matchType"    => "any-0",
                        "matchFields"  => ["notifyNo", "bidName"],
                        "filters"      => [
                            [
                                "fieldName"   => "publicDate",
                                "searchType"  => "range",
                                "from"        => $fromIso,
                                "to"          => $toIso
                            ],
                            [
                                "fieldName"   => "isDomestic",
                                "searchType"  => "in",
                                "fieldValues" => [1]
                            ],
                            [
                                "fieldName"   => "type",
                                "searchType"  => "in",
                                "fieldValues" => ["es-notify-contractor"]
                            ],
                            [
                                "fieldName"   => "isMedicine",
                                "searchType"  => "in",
                                "fieldValues" => [1]
                            ],
                            [
                                "fieldName"   => "caseKHKQ",
                                "searchType"  => "not_in",
                                "fieldValues" => ["1"]
                            ],
                            [
                                "fieldName"   => "isInternet",
                                "searchType"  => "in",
                                "fieldValues" => [1]
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->info("⏳ Đang gọi API HSMT...");

        $result = $service->searchSmartV2($payload);

        $status = $result['status'] ?? 0;
        $data   = $result['data']  ?? null;
        //dd($data['page']['content']);
        $this->info("HTTP Status: {$status}");

        if ($status !== 200) {
            $this->error("❌ Lỗi khi gọi API");
            $this->line($result['raw']);
            return;
        }

        // In ra số lượng kết quả
        $total = $data['page']['totalElements'] ?? 0;
        $this->info("✅ Tổng kết quả tìm thấy: {$total}");

        // Hiển thị danh sách ngắn gọn
        if (!empty($data['page']['content'])) {
            foreach ($data['page']['content'] as $item) {
                $this->line("-----------------------------------------------------");
                $this->info("📌 Gói thầu: " . ($item['bidName'][0] ?? 'N/A'));
                $this->line("Mã TBMT: " . ($item['notifyNo'] ?? 'N/A'));
                $this->line("Ngày đăng tải: " . ($item['publicDate'] ?? 'N/A'));
                $this->line("Thời điểm đóng thầu: " . ($item['bidOpenDate'] ?? 'N/A'));
                $this->line("Mã Bên mời thầu: " . ($item['investorCode'] ?? 'N/A'));
                $this->line("Bên mời thầu: " . ($item['investorName'] ?? 'N/A'));
                $this->line("Địa điểm: " . ($item['locations'][0]['districtName'] ?? '').' - '.($item['locations'][0]['provName'] ?? ''));
            }
        } else {
            $this->warn("⚠️ Không có dữ liệu trả về!");
        }

        return 0;
    }
}
