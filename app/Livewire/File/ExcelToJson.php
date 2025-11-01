<?php

namespace App\Livewire\File;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelToJson extends Component
{
    public $excelFiles = [];
    public $selected = [];
    public $selectAll = false;

    public $renameFile = null;     // file đang được đổi tên
    public $newFileName = '';      // tên mới nhập từ input

    public function mount()
    {
        $this->loadExcelFiles();
    }

    public function loadExcelFiles()
    {
        $excelDir = storage_path('app/public/excel');
        $jsonDir = storage_path('app/public/json');

        if (!is_dir($excelDir)) mkdir($excelDir, 0775, true);
        if (!is_dir($jsonDir)) mkdir($jsonDir, 0775, true);

        $files = collect(glob($excelDir . '/*.xlsx'))->map(function ($path) use ($jsonDir) {
            $excelName = basename($path);
            $jsonName = Str::replaceLast('.xlsx', '.json', $excelName);
            $jsonPath = $jsonDir . '/' . $jsonName;

            return [
                'name' => $excelName,
                'size' => round(filesize($path) / 1024, 2) . ' KB',
                'updated' => date('d/m/Y H:i', filemtime($path)),
                'json_name' => $jsonName,
                'json_exists' => file_exists($jsonPath),
            ];
        });

        $this->excelFiles = $files->toArray();
        $this->selected = [];
        $this->selectAll = false;
        $this->renameFile = null;
        $this->newFileName = '';
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? collect($this->excelFiles)->pluck('name')->toArray() : [];
    }

    public function convertToJson($fileName)
    {
        try {
            $excelPath = storage_path("app/public/excel/{$fileName}");
            if (!file_exists($excelPath)) {
                session()->flash('error', "Không tìm thấy file {$fileName}");
                return;
            }

            $spreadsheet = IOFactory::load($excelPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $headers = array_values($rows[0]);
            $data = [];

            foreach (array_slice($rows, 1) as $row) {
                $item = [];
                foreach ($headers as $i => $key) {
                    $item[$key] = $row[chr(65 + $i)] ?? null;
                }
                $data[] = $item;
            }

            $jsonDir = storage_path('app/public/json');
            if (!is_dir($jsonDir)) mkdir($jsonDir, 0775, true);

            $jsonName = Str::replaceLast('.xlsx', '.json', $fileName);
            $jsonPath = $jsonDir . '/' . $jsonName;
            file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            session()->flash('message', "✅ Đã convert {$fileName} → json/{$jsonName}");
            $this->redirectRoute('file.index');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Lỗi convert: ' . $e->getMessage());
        }

        $this->loadExcelFiles();
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            session()->flash('error', '⚠️ Chưa chọn file nào để xóa.');
            return;
        }

        foreach ($this->selected as $fileName) {
            $excelPath = storage_path("app/public/excel/{$fileName}");
            $jsonPath = storage_path('app/public/json/' . Str::replaceLast('.xlsx', '.json', $fileName));

            if (file_exists($excelPath)) File::delete($excelPath);
            if (file_exists($jsonPath)) File::delete($jsonPath);
        }

        session()->flash('message', "🗑️ Đã xóa " . count($this->selected) . " file và JSON tương ứng.");

        //$this->loadExcelFiles();
        $this->redirectRoute('file.index');
    }

    /** 🔹 Hiển thị ô nhập đổi tên */
    public function startRename($fileName)
    {
        $this->renameFile = $fileName;
        $this->newFileName = Str::replaceLast('.xlsx', '', $fileName);
    }

    /** 🔹 Thực hiện đổi tên */
    public function renameFileConfirm()
    {
        $oldName = $this->renameFile;
        $newName = trim($this->newFileName);

        if (!$oldName || !$newName) {
            session()->flash('error', 'Tên file không hợp lệ!');
            return;
        }

        if (!Str::endsWith($newName, '.xlsx')) {
            $newName .= '.xlsx';
        }

        $excelOld = storage_path("app/public/excel/{$oldName}");
        $excelNew = storage_path("app/public/excel/{$newName}");
        $jsonOld = storage_path('app/public/json/' . Str::replaceLast('.xlsx', '.json', $oldName));
        $jsonNew = storage_path('app/public/json/' . Str::replaceLast('.xlsx', '.json', $newName));

        if (file_exists($excelNew)) {
            session()->flash('error', 'Tên file mới đã tồn tại!');
            return;
        }

        try {
            if (file_exists($excelOld)) {
                rename($excelOld, $excelNew);
            }

            if (file_exists($jsonOld)) {
                rename($jsonOld, $jsonNew);
            }

            session()->flash('message', "✅ Đã đổi tên {$oldName} → {$newName}");
        } catch (\Throwable $e) {
            session()->flash('error', 'Lỗi khi đổi tên: ' . $e->getMessage());
        }

        $this->renameFile = null;
        $this->newFileName = ''; 
        //$this->loadExcelFiles();
        $this->redirectRoute('file.index');
    }

    public function cancelRename()
    {
        $this->renameFile = null;
        $this->newFileName = '';
    }

    public function render()
    {
        return view('livewire.file.excel-to-json', [
            'excelFiles' => $this->excelFiles,
        ]);
    }
}
