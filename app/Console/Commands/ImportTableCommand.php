<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportTableCommand extends Command
{
    protected $signature = 'import:table {file} {--update : Cập nhật và thêm mới thay vì xóa toàn bộ dữ liệu}';
    protected $description = 'Import dữ liệu từ file .mysql vào database. Dùng --update để update/thêm mới thay vì xóa.';

    public function handle()
    {
        $file = $this->argument('file');
        $isUpdate = $this->option('update');
        $path = database_path("exports/{$file}");

        if (!file_exists($path)) {
            $this->error("❌ File '{$path}' không tồn tại.");
            return Command::FAILURE;
        }

        $table = pathinfo($file, PATHINFO_FILENAME);
        $sql = file_get_contents($path);

        [$createSql, $insertSql] = $this->splitSqlParts($sql);

        // Tạo bảng nếu chưa tồn tại
        if (!Schema::hasTable($table)) {
            $this->info("🧱 Bảng '{$table}' chưa có — tiến hành tạo mới...");
            try {
                DB::unprepared($createSql);
                $this->info("✅ Đã tạo bảng '{$table}' thành công!");
            } catch (\Exception $e) {
                $this->error("⚠️ Lỗi khi tạo bảng: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // ✅ FIX: Tạm tắt kiểm tra foreign key khi truncate
        if (!$isUpdate) {
            $this->warn("⚠️ Chế độ làm mới: sẽ xóa toàn bộ dữ liệu trong bảng '{$table}' trước khi import...");
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table($table)->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $e) {
                $this->error("⚠️ Lỗi khi TRUNCATE bảng '{$table}': " . $e->getMessage());
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                return Command::FAILURE;
            }
        }

        // Xử lý các dòng INSERT
        $lines = array_filter(array_map('trim', explode(";\n", $insertSql)));

        foreach ($lines as $line) {
            if (stripos($line, 'INSERT INTO') === false) continue;

            // Nếu có --update thì chuyển thành INSERT ... ON DUPLICATE KEY UPDATE
            if ($isUpdate) {
                $line = $this->convertInsertToUpsert($line, $table);
            }

            try {
                DB::unprepared($line);
            } catch (\Exception $e) {
                $this->error("⚠️ Lỗi khi import dòng:\n{$line}\n➡ " . $e->getMessage());
            }
        }

        $this->info("✅ Hoàn tất import dữ liệu cho bảng '{$table}' " . ($isUpdate ? '(chế độ cập nhật)' : '(làm mới)'));
        return Command::SUCCESS;
    }

    private function splitSqlParts($sql)
    {
        $parts = preg_split('/;\s*\n/', $sql, 2);
        $createSql = $parts[0] ?? '';
        $insertSql = $parts[1] ?? '';
        return [$createSql, $insertSql];
    }

    private function convertInsertToUpsert($insertLine, $table)
    {
        preg_match('/\((.*?)\)\s*VALUES/s', $insertLine, $matches);
        if (!isset($matches[1])) return $insertLine;

        $columns = array_map('trim', explode(',', str_replace('`', '', $matches[1])));
        $updates = [];

        foreach ($columns as $col) {
            $updates[] = "`$col` = VALUES(`$col`)";
        }

        return rtrim($insertLine, ';') . " ON DUPLICATE KEY UPDATE " . implode(', ', $updates) . ";";
    }
}
