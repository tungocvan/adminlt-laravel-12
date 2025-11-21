<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MuaSamCongService;

class TestHSMTCommand extends Command
{
    protected $signature = 'mscong:test-hsmt {keyword?} {page=1} {size=20}';
    protected $description = 'Test tra cứu Hồ Sơ Mời Thầu trên Mua Sắm Công';

    public function handle(MuaSamCongService $service)
    {
        $keyword = $this->argument('keyword') ?? '';
        $page    = (int) $this->argument('page');
        $size    = (int) $this->argument('size');

        $this->info("🔍 Đang tìm kiếm HSMT với từ khóa: '{$keyword}' (page: $page, size: $size)");

        $payload = [
            "pageSize" => $size,
            "pageNumber" => $page,
            "query" => $keyword,
            "type" => 1,          // 1 = HSMT
            "bidType" => null,
            "bidField" => null,
            "investorName" => null,
            "procuringEntityName" => null,
            "startDate" => null,
            "endDate" => null
        ];

        $result = $service->searchBidNotice($payload);

        if (!$result['success']) {
            $this->error("❌ API lỗi!");
            $this->error("Status: " . ($result['status'] ?? 'N/A'));
            $this->line($result['body'] ?? $result['error']);
            return Command::FAILURE;
        }

        $data = $result['data'];

        $this->info("✅ Lấy được " . count($data['bido_notices'] ?? []) . " kết quả.\n");

        foreach ($data['bido_notices'] ?? [] as $item) {
            $this->line("• " . ($item['bidName'] ?? '[Không có tên]'));
        }

        $this->info("\n🎯 Hoàn thành.");
        return Command::SUCCESS;
    }
}
