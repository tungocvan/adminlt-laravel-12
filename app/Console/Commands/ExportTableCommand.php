<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportTableCommand extends Command
{
    protected $signature = 'export:table {table}';
    protected $description = 'Xuất toàn bộ dữ liệu của bảng ra file .mysql (chuẩn safe import)';

    public function handle()
    {
        $table = $this->argument('table');

        if (!Schema::hasTable($table)) {
            $this->error("❌ Bảng '{$table}' không tồn tại.");
            return Command::FAILURE;
        }

        $columns = Schema::getColumnListing($table);
        $rows = DB::table($table)->get();

        $fileName = database_path("exports/{$table}.mysql");
        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (\n";
        $columnsInfo = DB::select("SHOW COLUMNS FROM `{$table}`");

        $fields = [];
        foreach ($columnsInfo as $col) {
            $field = "  `{$col->Field}` {$col->Type}";
            if ($col->Null === 'NO') $field .= " NOT NULL";
            if ($col->Default !== null) $field .= " DEFAULT '" . addslashes($col->Default) . "'";
            if ($col->Extra) $field .= " {$col->Extra}";
            $fields[] = $field;
        }
        $sql .= implode(",\n", $fields) . "\n);\n\n";

        // ✅ Xuất dữ liệu từng dòng INSERT (đảm bảo JSON, ký tự đặc biệt, xuống dòng an toàn)
        foreach ($rows as $row) {
            $values = array_map(function ($val) {
                if ($val === null) return 'NULL';
                // Convert object -> JSON string nếu cần
                if (is_array($val) || is_object($val)) {
                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                }
                // Escape an toàn
                $val = str_replace(["\\", "'", "\r", "\n"], ["\\\\", "\\'", "\\r", "\\n"], $val);
                return "'" . $val . "'";
            }, (array)$row);

            $sql .= "INSERT INTO `{$table}` (`" . implode("`,`", $columns) . "`) VALUES (" . implode(",", $values) . ");\n";
        }

        file_put_contents($fileName, $sql);
        $this->info("✅ Đã xuất bảng '{$table}' ra file: {$fileName}");

        return Command::SUCCESS;
    }
}
