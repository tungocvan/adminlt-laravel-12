<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\TnvUserHelper;
use Illuminate\Support\Facades\Storage;

class ImportUsersCommand extends Command
{
    /**
     * Tên và cú pháp của command.
     *
     * php artisan import:users <file-path>
     */
    protected $signature = 'import:users {file : Đường dẫn đến file Excel (.xlsx hoặc .xls)}';

    /**
     * Mô tả command (hiển thị khi chạy php artisan list)
     */
    protected $description = 'Import danh sách user từ file Excel (xlsx/xls) vào hệ thống';

    /**
     * Thực thi command
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        // --- Kiểm tra file tồn tại ---
        if (!file_exists($filePath)) {
            $this->error("❌ File không tồn tại tại đường dẫn: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("🔄 Đang import file: {$filePath} ...");

        // --- Gọi hàm helper ---
        try {
            // Tạo instance file upload giả định (vì helper nhận UploadedFile)
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $filePath,
                basename($filePath),
                mime_content_type($filePath),
                null,
                true // true = test mode (bỏ qua check realpath)
            );

            $result = TnvUserHelper::importUsersFromExcel($uploadedFile);

            if ($result['status']) {
                $this->info("✅ {$result['message']}");

                if (!empty($result['errors'])) {
                    $this->warn("⚠️ Có lỗi ở một số dòng:");
                    foreach ($result['errors'] as $error) {
                        $this->warn(" - Dòng {$error['row']}: {$error['error']}");
                    }
                }

                $this->info("Tổng cộng: {$result['imported_count']} user được import.");
                return Command::SUCCESS;
            } else {
                $this->error("❌ Lỗi: {$result['message']}");
                return Command::FAILURE;
            }

        } catch (\Throwable $e) {
            $this->error("❌ Đã xảy ra lỗi: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
