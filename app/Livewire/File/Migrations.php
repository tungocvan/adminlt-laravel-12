<?php

namespace App\Livewire\File;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericImport;

class Migrations extends Component
{
    public $migrations = [];
    public $groupedMigrations = [];

    // Modal state
    public $modalVisible = false;
    public $selectedTable = null;
    public $tablesToDrop = [];

    public function mount()
    {
        $this->loadMigrations();
    }

    public function loadMigrations()
    {
        $this->migrations = DB::table('migrations')->orderBy('id', 'desc')->get();
        $this->groupMigrations();
    }

    private function groupMigrations()
    {
        $grouped = [];

        foreach ($this->migrations as $migration) {
            $tables = $this->getTablesFromMigrationFile($migration->migration);
            foreach ($tables as $table) {
                if (!isset($grouped[$table])) {
                    $grouped[$table] = [];
                }
                $grouped[$table][] = $migration;
            }
        }

        $this->groupedMigrations = $grouped;
    }

    private function getTablesFromMigrationFile($migrationName)
    {
        $files = File::files(database_path('migrations'));
        $tables = [];

        foreach ($files as $file) {
            if (str_contains($file->getFilename(), $migrationName)) {
                $content = File::get($file->getPathname());
                preg_match_all('/Schema::(?:create|table)\([\'"](.+)[\'"]/', $content, $matches);
                if (!empty($matches[1])) {
                    $tables = array_merge($tables, $matches[1]);
                }
            }
        }

        return $tables;
    }

    // Mở modal xác nhận trước khi xóa
    public function confirmDelete($table)
    {
        $this->selectedTable = $table;
        $this->tablesToDrop = [$table];
        $this->modalVisible = true;
    }

    // Hủy modal
    public function cancelDelete()
    {
        $this->modalVisible = false;
        $this->selectedTable = null;
        $this->tablesToDrop = [];
    }

    // Xóa bảng, migrate lại và import dữ liệu Excel
    public function deleteTableMigrations($table = null)
    {
        $table = $table ?? $this->selectedTable;
        if (!$table) return;

        $path = storage_path("app/public/excel/database/{$table}.xlsx");

        // Kiểm tra file Excel
        if (!File::exists($path)) {
            session()->flash('error', "Không thể xóa bảng '$table' vì chưa có file Excel để import.");
            $this->modalVisible = false;
            return;
        }

        // --- Bắt đầu drop bảng và các bảng phụ thuộc ---
        Schema::disableForeignKeyConstraints(); // Tắt kiểm tra FK tạm thời

        $this->dropTableWithDependencies($table);

        Schema::enableForeignKeyConstraints(); // Bật lại kiểm tra FK

        // --- Migrate bảng chính + các bảng liên quan ---
        try {
            Artisan::call('migrate'); // Chỉ migrate những migration chưa có
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }

        // --- Import dữ liệu từ Excel ---
        Excel::import(new GenericImport($table), $path);

        // --- Reset trạng thái modal ---
        $this->modalVisible = false;
        $this->selectedTable = null;
        $this->tablesToDrop = [];

        $this->loadMigrations();
        session()->flash('message', "Bảng '$table' đã xóa, migrate lại và import dữ liệu từ Excel thành công!");
    }


    /**
     * Drop bảng và tất cả bảng phụ thuộc đệ quy
     */
    private function dropTableWithDependencies($table)
    {
        // Lấy danh sách các bảng phụ thuộc trực tiếp
        $dependentTables = DB::select(
            "SELECT TABLE_NAME 
         FROM information_schema.KEY_COLUMN_USAGE 
         WHERE REFERENCED_TABLE_SCHEMA = DATABASE() 
           AND REFERENCED_TABLE_NAME = ?",
            [$table]
        );

        $dependentTables = array_map(fn($row) => $row->TABLE_NAME, $dependentTables);

        // Drop các bảng phụ thuộc đệ quy trước
        foreach ($dependentTables as $dep) {
            $this->dropTableWithDependencies($dep);
        }

        // Drop bảng chính
        if (Schema::hasTable($table)) {
            Schema::dropIfExists($table);
        }

        // Xóa migration liên quan
        if (isset($this->groupedMigrations[$table])) {
            foreach ($this->groupedMigrations[$table] as $migration) {
                DB::table('migrations')->where('migration', $migration->migration)->delete();
            }
        }
    }



    public function render()
    {
        return view('livewire.file.migrations');
    }
}
