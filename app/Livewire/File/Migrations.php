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
    public $search = '';
    public $importedTables = [];

    public function mount()
    {
        $this->loadMigrations();
    }

    public function loadMigrations()
    {
        $this->loadImportedTables(); // ✅ luôn cập nhật trạng thái import
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
            $tables = $this->getTablesFromMigrationFileWithStatus($migration->migration);

            foreach ($tables as $tableData) {
                $table = $tableData['table'];

                // Lấy quan hệ foreign key
                $relations = $this->getTableRelations($table);
                $imported = in_array(strtolower($table), array_map('strtolower', $this->importedTables));


                if (!isset($grouped[$table])) {
                    $grouped[$table] = [
                        'migrations' => [],
                        'exists' => $tableData['exists'],
                        'note' => $tableData['note'],
                        'references_to' => $relations['references_to'],
                        'referenced_by' => $relations['referenced_by'],
                        'imported' => $imported,
                    ];
                }

                $grouped[$table]['migrations'][] = $migration;
            }
        }

        $this->groupedMigrations = $grouped;
    }



    private function getTablesFromMigrationFileWithStatus($migrationName)
    {
        $tables = [];

        // Quét core migrations
        $tables = array_merge($tables, $this->scanMigrationPath(database_path('migrations'), $migrationName));

        // Quét modules
        $modulesPath = base_path('Modules');
        if (File::exists($modulesPath)) {
            $moduleDirs = File::directories($modulesPath);
            foreach ($moduleDirs as $moduleDir) {
                $migrationPath = $moduleDir . '/database/migrations';
                if (File::exists($migrationPath)) {
                    $tables = array_merge($tables, $this->scanMigrationPath($migrationPath, $migrationName));
                }
            }
        }

        // Loại bỏ bảng trùng lặp
        $tables = array_unique($tables);

        // Kiểm tra tồn tại trong database
        $tablesWithStatus = [];
        foreach ($tables as $table) {
            $exists = Schema::hasTable($table);
            $tablesWithStatus[] = [
                'table' => $table,
                'exists' => $exists,
                'note' => $exists ? 'Tồn tại' : 'Chưa tồn tại'
            ];
        }

        return $tablesWithStatus;
    }

    private function scanMigrationPath($path, $migrationName)
    {
        $tables = [];
        foreach (File::files($path) as $file) {
            if (str_contains($file->getFilename(), $migrationName)) {
                $content = File::get($file->getPathname());
                preg_match_all('/Schema::(?:create|table)\([\'"](.+?)[\'"]/', $content, $matches);
                if (!empty($matches[1])) {
                    $tables = array_merge($tables, $matches[1]);
                }
            }
        }
        return $tables;
    }

    public function getFilteredMigrationsProperty()
    {
        if (empty($this->search)) {
            return $this->groupedMigrations;
        }

        return collect($this->groupedMigrations)
            ->filter(fn($data, $table) => str_contains(strtolower($table), strtolower($this->search)))
            ->toArray();
    }

    public function loadImportedTables()
    {
        // Ví dụ: đọc từ file JSON
        $file = storage_path('app/public/mysql/imported_tables.json');
        if (File::exists($file)) {
            $this->importedTables = json_decode(File::get($file), true);
        } else {
            $this->importedTables = [];
        }

        // Debug xem có đúng không
        //dd($this->importedTables);
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
        try {
            Artisan::call('import:table', [
                'table' => strtolower($tableName)
            ]);
            $output = Artisan::output();
            session()->flash('message', $output);

            // ✅ Cập nhật trạng thái import
            $file = storage_path('app/public/mysql/imported_tables.json');

            // Load danh sách hiện tại
            $importedTables = File::exists($file)
                ? json_decode(File::get($file), true)
                : [];

            $tableNameLower = strtolower($tableName);

            // Nếu chưa có thì thêm
            if (!in_array($tableNameLower, array_map('strtolower', $importedTables))) {
                $importedTables[] = $tableNameLower;
                File::put($file, json_encode($importedTables, JSON_PRETTY_PRINT));
            }

            // Reload danh sách import
            $this->loadImportedTables();
            $this->groupMigrations();
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi import: " . $e->getMessage());
        }
    }
    public function getExportFileInfo($tableName)
    {
        $filePath = storage_path("app/public/mysql/{$tableName}.mysql");

        if (File::exists($filePath)) {
            return [
                'path' => $filePath,
                'size' => File::size($filePath),
                'modified' => date('d/m/Y H:i:s', File::lastModified($filePath)),
            ];
        }

        return null;
    }

    public function backupDatabase()
    {
        // 8️⃣ Import mysql 
        $database = env('DB_DATABASE');
        $fileName = storage_path('app/public/mysql') . "/{$database}.mysql";
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
