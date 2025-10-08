<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvHelper;

class ExportExcelCommand extends Command
{
    /**
     * Tên lệnh (gọi từ terminal)
     *
     * Ví dụ: php artisan export:excel "App\Models\User"
     */
    protected $signature = 'export:excel
        {model : Tên đầy đủ của model, ví dụ App\\Models\\User}
        {--ids= : Danh sách ID, ví dụ: 1,2,3}
        {--fields= : Danh sách field, ví dụ: id,name,email hoặc id:ID,name:Họ tên}
        {--title="BÁO CÁO DỮ LIỆU" : Tiêu đề file Excel}
        {--footer="NGƯỜI LẬP BẢNG" : Dòng footer}';

    /**
     * Mô tả lệnh
     */
    protected $description = 'Xuất dữ liệu từ model ra file Excel (tổng quát)';

    /**
     * Thực thi command
     */
    public function handle()
    {
        $modelClass = $this->argument('model');
        $idsOption = $this->option('ids');
        $fieldsOption = $this->option('fields');
        $title = $this->option('title');
        $footer = $this->option('footer');

        // ===== 1️⃣ Xử lý ID
        $ids = [];
        if (!empty($idsOption)) {
            $ids = array_filter(array_map('trim', explode(',', $idsOption)));
        }

        // ===== 2️⃣ Xử lý Fields
        $fields = [];
        if (!empty($fieldsOption)) {
            // Dạng id,name,email
            if (!str_contains($fieldsOption, ':')) {
                $fields = array_filter(array_map('trim', explode(',', $fieldsOption)));
            } else {
                // Dạng id:ID,name:Họ tên
                $pairs = array_filter(array_map('trim', explode(',', $fieldsOption)));
                foreach ($pairs as $pair) {
                    [$key, $value] = array_pad(explode(':', $pair, 2), 2, null);
                    if ($key) $fields[$key] = $value ?? ucfirst($key);
                }
            }
        }

        // ===== 3️⃣ Gọi hàm exportToExcel
        $this->info("🔄 Đang xuất dữ liệu từ model: {$modelClass} ...");

        $result = TnvHelper::exportToExcel(
            $modelClass,
            $ids,
            $fields,
            $title,
            $footer
        );

        // ===== 4️⃣ Hiển thị kết quả
        if ($result['status']) {
            $this->newLine();
            $this->info("✅ Xuất file thành công!");
            $this->line("📦 File: " . $result['path']);
            $this->line("📊 Tổng số bản ghi: " . $result['count']);
        } else {
            $this->error("❌ Lỗi: " . $result['message']);
        }

        return 0;
    }
}
