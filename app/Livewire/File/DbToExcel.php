<?php

namespace App\Livewire\File;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class DbToExcel extends Component
{
    public $tables = [];
    public $search = '';
    public $selectedTables = [];
    public $selectAll = false;

    public $message;
    public $alertType = 'success';

    public function mount()
    {
        // Tạo thư mục lưu file nếu chưa tồn tại
        // Đảm bảo thư mục luôn tồn tại
        $excelDir = storage_path('app/public/excel/database');
        if (!is_dir($excelDir)) mkdir($excelDir, 0775, true);
        $this->loadTables();
    }

    public function loadTables()
    {
        $rawTables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . env('DB_DATABASE');
        $tables = [];

        foreach ($rawTables as $t) {
            $tableName = $t->$key;
            $fileExists = Storage::disk('public')->exists("excel/database/{$tableName}.xlsx");


            $tables[] = [
                'name' => $tableName,
                'exists' => $fileExists,
                'excel_path' => asset("storage/excel/database/{$tableName}.xlsx"),
            ];
        }
        //dd( $tables );
        // Lọc theo search
        if ($this->search) {
            $tables = array_filter($tables, fn($table) => stripos($table['name'], $this->search) !== false);
        }

        $this->tables = array_values($tables);
    }

    public function updatedSearch()
    {
        $this->loadTables();
    }

    public function export($tableName)
    {
        
        $rows = DB::table($tableName)->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($rows->isEmpty()) {
            $sheet->setCellValue('A1', 'Bảng trống');
        } else {
            // 1️⃣ Lấy tất cả cột, tối đa 200 cột
            $columns = array_keys((array)$rows->first());
            $columns = array_slice($columns, 0, 200);
            $colCount = count($columns);

            // 2️⃣ Header: bold + center + wrap text
            for ($colIndex = 1; $colIndex <= $colCount; $colIndex++) {
                $sheet->setCellValueByColumnAndRow($colIndex, 1, $columns[$colIndex - 1]);
                $sheet->getStyleByColumnAndRow($colIndex, 1)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($colIndex, 1)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
            }

            // 3️⃣ Dữ liệu: wrap text tất cả ô
            foreach ($rows as $rowIndex => $row) {
                $col = 1;
                foreach ($columns as $column) {
                    $sheet->setCellValueByColumnAndRow($col, $rowIndex + 2, $row->$column ?? '');
                    $sheet->getStyleByColumnAndRow($col, $rowIndex + 2)
                        ->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $col++;
                }
            }

            // 4️⃣ Auto row height
            $highestRow = $sheet->getHighestRow();
            for ($r = 1; $r <= $highestRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(-1);
            }

            // 5️⃣ Tính width thủ công + hệ số + giới hạn max 120
            $factor = 1.2; // hệ số cân chỉnh font, tăng nếu chữ dài hoặc nhiều dấu
            for ($colIndex = 0; $colIndex < $colCount; $colIndex++) {
                $maxLen = strlen($columns[$colIndex]); // header

                foreach ($rows as $row) {
                    $value = $row->{$columns[$colIndex]} ?? '';
                    $len = intval(strlen($value) * $factor);
                    $maxLen = max($maxLen, $len);
                }

                $width = min($maxLen + 2, 20); // padding + max 120
                $sheet->getColumnDimensionByColumn($colIndex + 1)->setWidth($width);
            }

            // 6️⃣ Khung toàn bộ bảng
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                ->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        // 7️⃣ Lưu file
        $path = "public/excel/database/{$tableName}.xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(storage_path("app/{$path}"));
        
        // 8️⃣  Cập nhật trạng thái hiển thị
     
        $this->loadTables();

        $this->message = "✅ Đã xuất bảng <b>{$tableName}</b> thành công!";
        $this->alertType = 'success';
    }

    public function exportMyslq($tableName){
        // 8️⃣ Xuất mysql 
        
        try {
            Artisan::call('export:table', [
                'table' => strtolower($tableName)
            ]); 
            $output = Artisan::output();
            $this->message = $output ;
            $this->alertType = 'success';
        } catch (\Exception $e) {
            session()->flash('error', "Lỗi migrate: " . $e->getMessage());
        }
    }


    public function deleteFile($tableName)
    {

        $excelPath = storage_path("app/public/excel/database/{$tableName}.xlsx");

        if (file_exists($excelPath)) {
            File::delete($excelPath);
            $this->message = "🗑️ Đã xóa file Excel của bảng <b>{$tableName}</b>.";
            $this->alertType = 'info';
        } else {
            $this->message = "⚠️ Không tìm thấy file để xóa.";
            $this->alertType = 'warning';
        }

        $this->selectedTables = array_diff($this->selectedTables, [$tableName]);
        $this->loadTables();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTables = collect($this->tables)
                ->filter(fn($t) => $t['exists'])
                ->pluck('name')
                ->toArray();
        } else {
            $this->selectedTables = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedTables)) {
            $this->message = "⚠️ Bạn chưa chọn bảng nào để xóa.";
            $this->alertType = 'warning';
            return;
        }

        foreach ($this->selectedTables as $table) {
            $path = "public/excel/database/{$table}.xlsx";
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $this->message = "🗑️ Đã xóa " . count($this->selectedTables) . " file Excel.";
        $this->alertType = 'info';
        $this->selectedTables = [];
        $this->selectAll = false;
        $this->loadTables();
    }

    public function render()
    {
        return view('livewire.file.db-to-excel');
    }
}
