<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TnvHelper
{
    /**
     * Hàm xuất Excel tổng quát
     *
     * @param string|Model $modelClass  Model class, ví dụ: App\Models\User::class
     * @param array $ids                Danh sách ID (nếu có)
     * @param array $fields             Mảng field hoặc key-value mapping label
     * @param string $title             Tiêu đề báo cáo
     * @param string $footer            Chữ ký hoặc footer
     * @return array
     */
    public static function exportToExcel(
        string|Model $modelClass,
        array $ids = [],
        array $fields = [],
        string $title = 'BÁO CÁO DỮ LIỆU',
        string $footer = 'NGƯỜI LẬP BẢNG'
    ) 
        {
            try {
                // ===== 1️⃣ Lấy dữ liệu model =====
                if (!class_exists($modelClass)) {
                    return ['status' => false, 'message' => "Model {$modelClass} không tồn tại."];
                }

                $query = $modelClass::query();

                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }

                $items = $query->get();

                if ($items->isEmpty()) {
                    return ['status' => false, 'message' => 'Không có dữ liệu để xuất.'];
                }

                // ===== 2️⃣ Xử lý fields & labels =====
                $exportFields = [];
                $fieldLabels = [];

                // Nếu là mảng key-value: ['id' => 'ID', 'name' => 'Họ tên']
                if (!empty($fields) && array_keys($fields) !== range(0, count($fields) - 1)) {
                    $fieldLabels = $fields;
                    $exportFields = array_keys($fields);
                }
                // Nếu là mảng chỉ có tên field: ['id','name','email']
                else {
                    $exportFields = !empty($fields) ? $fields : array_keys($items->first()->getAttributes());
                    $fieldLabels = collect($exportFields)->mapWithKeys(fn($f) => [$f => ucfirst(str_replace('_', ' ', $f))])->toArray();
                }

                // ===== 3️⃣ Tạo file Excel =====
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Export');

                // --- Tiêu đề
                $titleText = $title . ' - ' . now()->format('d/m/Y');
                $sheet->mergeCells('A1:' . self::colLetter(count($exportFields)) . '1');
                $sheet->setCellValue('A1', $titleText);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- Header
                $headerRow = 3;
                $header = [];
                foreach ($exportFields as $f) {
                    $header[] = $fieldLabels[$f] ?? ucfirst(str_replace('_', ' ', $f));
                }
                $sheet->fromArray([$header], null, 'A' . $headerRow);

                $headerRange = 'A' . $headerRow . ':' . self::colLetter(count($exportFields)) . $headerRow;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEFEFEF'],
                    ],
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // ===== 4️⃣ Ghi dữ liệu =====
                $row = $headerRow + 1;
                foreach ($items as $item) {
                    $col = 1;
                    foreach ($exportFields as $field) {
                        $value = $item->$field ?? '';

                        if ($value instanceof \Carbon\Carbon) {
                            $value = $value->format('d/m/Y H:i');
                        } elseif (is_bool($value)) {
                            $value = $value ? 'Có' : 'Không';
                        } elseif ($value instanceof \Illuminate\Support\Collection) {
                            $value = $value->implode(', ');
                        } elseif (is_array($value)) {
                            // Nếu là mảng, ghép lại thành chuỗi hoặc JSON
                            $value = implode(', ', array_map('strval', $value));
                        } elseif (is_object($value)) {
                            // Nếu là object (vd: stdClass), encode JSON
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }

                        $sheet->setCellValueByColumnAndRow($col, $row, (string) $value);

                        $col++;
                    }
                    $row++;
                }

                // ===== 5️⃣ Style tổng thể =====
                $dataRange = 'A' . $headerRow . ':' . self::colLetter(count($exportFields)) . ($row - 1);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                foreach (range(1, count($exportFields)) as $colIndex) {
                    $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
                }

                // ===== 6️⃣ Footer =====
                $footerRow = $sheet->getHighestRow() + 2;
                $totalCols = count($exportFields);
                $startCol = self::colLetter(max(1, $totalCols - 2));
                $endCol = self::colLetter($totalCols);
                $sheet->mergeCells("{$startCol}{$footerRow}:{$endCol}{$footerRow}");
                $sheet->setCellValue("{$startCol}{$footerRow}", $footer);
                $footerStyle = $sheet->getStyle("{$startCol}{$footerRow}");
                $footerStyle->getFont()->setBold(true);
                $footerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ===== 7️⃣ Lưu file =====
                $timestamp = now()->format('Ymd_His');
                $modelName = strtolower(class_basename($modelClass));
                $fileName = "{$modelName}_export_{$timestamp}.xlsx";
                $exportPath = storage_path("app/exports/{$fileName}");

                if (!is_dir(dirname($exportPath))) {
                    mkdir(dirname($exportPath), 0777, true);
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save($exportPath);

                return [
                    'status' => true,
                    'message' => 'Xuất file thành công.',
                    'path' => $exportPath,
                    'count' => $items->count(),
                    'fields' => $exportFields,
                ];
            } catch (\Throwable $e) {
                return [
                    'status' => false,
                    'message' => 'Lỗi khi xuất file: ' . $e->getMessage(),
                ];
            }
        }

    /**
     * Helper: Chuyển số thành chữ cái cột (1 -> A, 27 -> AA)
     */
    private static function colLetter($index)
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intdiv($index - 1, 26);
        }
        return $letter;
    }

    public static function downloadFile(string $path, string $disk = 'public', ?string $downloadName = null)
    {
      
        try {
            if (!Storage::disk($disk)->exists($path)) {
                Log::warning("❌ File không tồn tại trên disk [{$disk}]: {$path}");
                return back()->with('error', '❌ File không tồn tại hoặc đã bị xóa.');
            }
            
            $fullPath = Storage::disk($disk)->path($path);
            $downloadName = $downloadName ?? basename($path);
            
            // ✅ Đảm bảo không có buffer làm hỏng file binary
            while (ob_get_level()) ob_end_clean();
            
            return response()->streamDownload(function () use ($fullPath) {
                $handle = fopen($fullPath, 'rb');
                fpassthru($handle);
                fclose($handle);
            }, $downloadName, [
                'Content-Type' => mime_content_type($fullPath) ?: 'application/octet-stream',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi khi tải file: ' . $e->getMessage(), [
                'path' => $path,
                'disk' => $disk,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', '⚠️ Xảy ra lỗi khi tải file.');
        }
    }

  
}
