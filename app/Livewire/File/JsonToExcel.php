<?php

namespace App\Livewire\File;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JsonToExcel extends Component
{
    public $jsonFiles = [];
    public $selected = [];
    public $selectAll = false;

    public $renameFile = null;
    public $newFileName = '';

    public function mount()
    {
        $this->loadJsonFiles();
    }

    public function loadJsonFiles()
    {
        $jsonDir = storage_path('app/public/json');
        $excelDir = storage_path('app/public/excel');

        if (!is_dir($jsonDir)) mkdir($jsonDir, 0775, true);
        if (!is_dir($excelDir)) mkdir($excelDir, 0775, true);

        $files = collect(glob($jsonDir . '/*.json'))->map(function ($path) use ($excelDir) {
            $jsonName = basename($path);
            $excelName = Str::replaceLast('.json', '.xlsx', $jsonName);
            $excelPath = $excelDir . '/' . $excelName;

            return [
                'name' => $jsonName,
                'size' => round(filesize($path) / 1024, 2) . ' KB',
                'updated' => date('d/m/Y H:i', filemtime($path)),
                'excel_name' => $excelName,
                'excel_exists' => file_exists($excelPath),
            ];
        });

        $this->jsonFiles = $files->toArray();
        $this->selected = [];
        $this->selectAll = false;
        $this->renameFile = null;
        $this->newFileName = '';
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? collect($this->jsonFiles)->pluck('name')->toArray() : [];
    }

    public function convertToExcel($fileName)
    {
        try {
            $jsonPath = storage_path("app/public/json/{$fileName}");
            if (!file_exists($jsonPath)) {
                session()->flash('error', "Không tìm thấy file {$fileName}");
                return;
            }

            $data = json_decode(file_get_contents($jsonPath), true);
            if (!is_array($data)) {
                session()->flash('error', "File {$fileName} không hợp lệ (không phải JSON array).");
                return;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            if (count($data) > 0) {
                $headers = array_keys($data[0]);
                $colIndex = 1;

                foreach ($headers as $header) {
                    $sheet->setCellValueByColumnAndRow($colIndex, 1, $header);
                    $colIndex++;
                }

                $row = 2;
                foreach ($data as $item) {
                    $col = 1;
                    foreach ($headers as $header) {
                        $sheet->setCellValueByColumnAndRow($col, $row, $item[$header] ?? '');
                        $col++;
                    }
                    $row++;
                }
            }

            $excelDir = storage_path('app/public/excel');
            if (!is_dir($excelDir)) mkdir($excelDir, 0775, true);

            $excelName = Str::replaceLast('.json', '.xlsx', $fileName);
            $excelPath = $excelDir . '/' . $excelName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($excelPath);

            session()->flash('message', "✅ Đã convert {$fileName} → excel/{$excelName}");
            $this->redirectRoute('file.index');
        } catch (\Throwable $e) {
            session()->flash('error', '❌ Lỗi convert: ' . $e->getMessage());
        }

        $this->loadJsonFiles();
    }

    public function deleteSelected()
    {
        if (empty($this->selected)) {
            session()->flash('error', '⚠️ Chưa chọn file nào để xóa.');
            return;
        }

        foreach ($this->selected as $fileName) {
            $jsonPath = storage_path("app/public/json/{$fileName}");
            $excelPath = storage_path('app/public/excel/' . Str::replaceLast('.json', '.xlsx', $fileName));

            if (file_exists($jsonPath)) File::delete($jsonPath);
            if (file_exists($excelPath)) File::delete($excelPath);
        }

        session()->flash('message', "🗑️ Đã xóa " . count($this->selected) . " file và Excel tương ứng.");

        //$this->loadJsonFiles();
        $this->redirectRoute('file.index');
    }

    /** 🔹 Hiển thị form đổi tên */
    public function startRename($fileName)
    {
        $this->renameFile = $fileName;
        $this->newFileName = Str::replaceLast('.json', '', $fileName);
    }

    /** 🔹 Đổi tên file JSON + Excel tương ứng */
    public function renameFileConfirm()
    {
        $oldName = $this->renameFile;
        $newName = trim($this->newFileName);

        if (!$oldName || !$newName) {
            session()->flash('error', 'Tên file không hợp lệ!');
            return;
        }

        if (!Str::endsWith($newName, '.json')) {
            $newName .= '.json';
        }

        $jsonOld = storage_path("app/public/json/{$oldName}");
        $jsonNew = storage_path("app/public/json/{$newName}");
        $excelOld = storage_path('app/public/excel/' . Str::replaceLast('.json', '.xlsx', $oldName));
        $excelNew = storage_path('app/public/excel/' . Str::replaceLast('.json', '.xlsx', $newName));

        if (file_exists($jsonNew)) {
            session()->flash('error', 'Tên file mới đã tồn tại!');
            return;
        }
 
        try {
            if (file_exists($jsonOld)) {
                rename($jsonOld, $jsonNew);
            }

            if (file_exists($excelOld)) {
                rename($excelOld, $excelNew);
            }

            session()->flash('message', "✅ Đã đổi tên {$oldName} → {$newName}");
        } catch (\Throwable $e) {
            session()->flash('error', 'Lỗi khi đổi tên: ' . $e->getMessage());
        }

        $this->renameFile = null;
        $this->newFileName = '';
       // $this->loadJsonFiles();
       $this->redirectRoute('file.index');
    }

    public function cancelRename()
    {
        $this->renameFile = null;
        $this->newFileName = '';
    }

    public function render()
    {
        return view('livewire.file.json-to-excel', [
            'jsonFiles' => $this->jsonFiles,
        ]);
    }
}
