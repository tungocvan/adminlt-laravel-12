<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class MakeUsersTemplateCommand extends Command
{
    /**
     * Tên và cú pháp của command.
     *
     * php artisan make:users-template
     */
    protected $signature = 'make:users-template 
                            {path? : Đường dẫn để lưu file (mặc định: storage/app/imports/users_template.xlsx)}';

    /**
     * Mô tả command
     */
    protected $description = 'Tạo file Excel mẫu (users_template.xlsx) để admin nhập danh sách user';

    /**
     * Thực thi command
     */
    public function handle()
    {
        try {
            // --- Xác định đường dẫn lưu file ---
            $path = $this->argument('path') ?? 'storage/app/imports/users_template.xlsx';

            // Tạo thư mục nếu chưa có
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // --- Tạo spreadsheet ---
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Users Template');

            // --- Header mẫu ---
            $headers = [
                'name',
                'email',
                'username',
                'password',
                'role_name',
                'is_admin',
                'verified'
            ];

            // --- Ghi header ---
            $colIndex = 1;
            foreach ($headers as $header) {
                $cell = $sheet->getCellByColumnAndRow($colIndex, 1);
                $cell->setValue($header);
                $sheet->getStyle($cell->getCoordinate())->getFont()->setBold(true);
                $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
                $colIndex++;
            }

            // --- Ghi 2 dòng mẫu ---
            $sampleData = [
                ['Nguyễn Văn A', 'a@gmail.com', 'vana', '123456', 'Admin', 1, 'yes'],
                ['Trần Thị B1', 'b1@gmail.com', 'btran1', '123456', 'User', 0, ''],
                ['Trần Thị B2', 'b2@gmail.com', 'btran2', '123456', 'User', 0, ''],
                ['Trần Thị B3', 'b3@gmail.com', 'btran3', '123456', 'User', 0, ''],
                ['Trần Thị B4', 'b4@gmail.com', 'btran4', '123456', 'User', 0, ''],
                ['Trần Thị B5', 'b5@gmail.com', 'btran5', '123456', 'User', 0, '']
            ];

            $rowIndex = 2;
            foreach ($sampleData as $row) {
                $colIndex = 1;
                foreach ($row as $value) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);
                    $colIndex++;
                }
                $rowIndex++;
            }

            // --- Ghi file ---
            $writer = new Xlsx($spreadsheet);
            $writer->save($path);

            $this->info("✅ File mẫu đã được tạo thành công tại:");
            $this->line(realpath($path));

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("❌ Lỗi khi tạo file mẫu: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
