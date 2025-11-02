<?php

namespace App\Livewire\File;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Migrations extends Component
{
    public $migrations = [];
    public $groupedMigrations = [];

    public $newTables = []; // Bảng mới add

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

        // Kiểm tra bảng mới add (chưa tồn tại trong database)
        $this->newTables = [];
        foreach ($this->groupedMigrations as $table => $migrations) {
            if (!Schema::hasTable($table)) {
                $this->newTables[] = $table;
            }
        }
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

    // Modal xác nhận bảng cũ
    public function confirmDelete($table)
    {
        $this->selectedTable = $table;
        $this->tablesToDrop = [$table];
        $this->modalVisible = true;
    }

    public function cancelDelete()
    {
        $this->modalVisible = false;
        $this->selectedTable = null;
        $this->tablesToDrop = [];
    }

    // Xóa bảng cũ & migrate lại
    public function deleteTableMigrations($table = null)
    {
        $table = $table ?? $this->selectedTable;
        if (!$table) return;

        try {
            Artisan::call('clean:table', [
                'table' => strtolower($table)
            ]);
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }

        try {
            Artisan::call('migrate');
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }

        $this->modalVisible = false;
        $this->selectedTable = null;
        $this->tablesToDrop = [];

        $this->loadMigrations();
        session()->flash('message', "Bảng '$table' đã xóa và migrate lại thành công!");
    }

    public function exportMyslq($tableName)
    {
        // 8️⃣ Xuất mysql 

        try {
            Artisan::call('export:table', [
                'table' => strtolower($tableName)
            ]);
            $output = Artisan::output();
            session()->flash('message', $output);
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }
    }
    public function importMyslq($tableName)
    {
        // 8️⃣ Import mysql 

        try {
            Artisan::call('import:table', [
                'table' => strtolower($tableName)
            ]);
            $output = Artisan::output();
            session()->flash('message', $output);
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }
    }
    public function backupDatabase()
    {
        // 8️⃣ Import mysql 
        $database = env('DB_DATABASE');
        $fileName = storage_path('app/public/mysql') . "/{$database}.mysql";;
        try {
            Artisan::call('db:mysql', [
                'action' => 'backup',
                'name' => $fileName
            ]);
            $output = Artisan::output();
            session()->flash('message', $output);
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }
    }
    public function restoreDatabase()
    {
        // 8️⃣ Import mysql 
        $database = env('DB_DATABASE');
        $fileName = storage_path('app/public/mysql') . "/{$database}.mysql";;
        try {
            Artisan::call('db:mysql', [
                'action' => 'restore',
                'name' => $fileName
            ]);
            $output = Artisan::output();
            session()->flash('message', $output);
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }
    }

    public function getTableRelations($table)
    {
        $referencedBy = DB::select("
        SELECT TABLE_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME = ?
    ", [$table]);

        $referencesTo = DB::select("
        SELECT REFERENCED_TABLE_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = ?
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ", [$table]);

        return [
            'referenced_by' => array_map(fn($r) => $r->TABLE_NAME, $referencedBy),
            'references_to' => array_map(fn($r) => $r->REFERENCED_TABLE_NAME, $referencesTo),
        ];
    }


    public function render()
    {
        return view('livewire.file.migrations');
    }
}
