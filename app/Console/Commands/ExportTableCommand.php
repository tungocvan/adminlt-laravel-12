<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class ExportTableCommand extends Command
{
    protected $signature = 'export:table {table}';
    protected $description = 'Xuất toàn bộ dữ liệu của bảng ra file .mysql (chuẩn safe import)';

    public function handle()
    {
        $table = $this->argument('table');

        try {
            // 1️⃣ Kiểm tra bảng có tồn tại
            if (!Schema::hasTable($table)) {
                $this->error("❌ Bảng '{$table}' không tồn tại.");
                return Command::FAILURE;
            }

            // 2️⃣ Lấy dữ liệu và thông tin cột
            $columns = Schema::getColumnListing($table);
            $rows = DB::table($table)->get();
            $columnsInfo = DB::select("SHOW COLUMNS FROM `{$table}`");

            // 3️⃣ Tạo thư mục lưu file nếu chưa có
            $mysqlDir = storage_path('app/public/mysql');
            if (!is_dir($mysqlDir)) {
                mkdir($mysqlDir, 0775, true);
            }

            // Kiểm tra quyền ghi
            if (!is_writable($mysqlDir)) {
                $this->error("❌ Không thể ghi vào thư mục: {$mysqlDir}");
                return Command::FAILURE;
            }

            $fileName = $mysqlDir . "/{$table}.mysql";

            // 4️⃣ Tạo SQL CREATE TABLE
            $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (\n";
            $fields = [];
            foreach ($columnsInfo as $col) {
                $field = "  `{$col->Field}` {$col->Type}";
                if ($col->Null === 'NO') $field .= " NOT NULL";
                if ($col->Default !== null) $field .= " DEFAULT '" . addslashes($col->Default) . "'";
                if ($col->Extra) $field .= " {$col->Extra}";
                $fields[] = $field;
            }
            $sql .= implode(",\n", $fields) . "\n);\n\n";

            // 5️⃣ Xuất dữ liệu từng dòng INSERT
            foreach ($rows as $row) {
                $values = array_map(function ($val) {
                    if ($val === null) return 'NULL';
                    if (is_array($val) || is_object($val)) {
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                    }
                    $val = str_replace(["\\", "'", "\r", "\n"], ["\\\\", "\\'", "\\r", "\\n"], $val);
                    return "'" . $val . "'";
                }, (array)$row);

                $sql .= "INSERT INTO `{$table}` (`" . implode("`,`", $columns) . "`) VALUES (" . implode(",", $values) . ");\n";
            }

            // 6️⃣ Ghi file
            file_put_contents($fileName, $sql);

            $this->info("✅ Đã xuất bảng '{$table}' ra file: {$fileName}");
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error("❌ Lỗi khi xuất bảng: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
