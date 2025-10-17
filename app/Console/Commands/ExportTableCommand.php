<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportTableCommand extends Command
{
    protected $signature = 'export:table {table}';
    protected $description = 'Xuất toàn bộ dữ liệu của bảng ra file .mysql';

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
            $fields[] = "  `{$col->Field}` {$col->Type}" .
                ($col->Null === 'NO' ? " NOT NULL" : "") .
                ($col->Default !== null ? " DEFAULT '{$col->Default}'" : "") .
                ($col->Extra ? " {$col->Extra}" : "");
        }
        $sql .= implode(",\n", $fields) . "\n);\n\n";

        foreach ($rows as $row) {
            $values = array_map(function ($val) {
                if ($val === null) return 'NULL';
                return "'" . addslashes($val) . "'";
            }, (array)$row);

            $sql .= "INSERT INTO `{$table}` (`" . implode("`,`", $columns) . "`) VALUES (" . implode(",", $values) . ");\n";
        }

        file_put_contents($fileName, $sql);

        $this->info("✅ Đã xuất bảng '{$table}' ra file: {$fileName}");

        return Command::SUCCESS;
    }
}
