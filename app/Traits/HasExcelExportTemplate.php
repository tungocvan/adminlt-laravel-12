<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


use Carbon\Carbon;

trait HasExcelExportTemplate
{
    /**
     * Xuất dữ liệu ra Excel từ file template có sẵn.
     */
    public function exportTemplate(array $options)
    {
        $templatePath = $options['templatePath'] ?? null;
        $sheetName    = $options['sheetName'] ?? 'Sheet1';
        $data         = $options['data'] ?? [];
        $columns      = $options['columns'] ?? [];
        $startRow     = $options['startRow'] ?? 10;

        if (!$templatePath || !file_exists($templatePath)) {
            abort(404, "Template Excel không tồn tại: {$templatePath}");
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if (!$sheet) {
            abort(400, "Không tìm thấy sheet '{$sheetName}' trong file template.");
        }

        // ====== 1️⃣ Ghi Titles (có thể nhiều dòng) ======
        if (!empty($options['titles'])) {
            foreach ($options['titles'] as $titleOption) {
                $this->applyTextStyle($sheet, $titleOption);
            }
        } elseif (!empty($options['title'])) {
            $this->applyTextStyle($sheet, $options['title']);
        }


        if (!empty($options['images'])) {
            foreach ($options['images'] as $imgOption) {
                $this->applyImage($sheet, $imgOption);
            }
        }
        

        // ====== 2️⃣ Ghi tiêu đề cột nếu có ======
        $currentRow = $startRow;
        $hasHeader = collect($columns)->contains(fn($col) => !empty($col['title']));

        if ($hasHeader) {
            $headerRow = $startRow - 1;
            $colIndex = 1;
        
            // Ghi cột STT
            $sheet->setCellValueByColumnAndRow($colIndex++, $headerRow, 'STT');
        
            foreach ($columns as $col) {
                $title = $col['title'] ?? strtoupper($col['field']);
                $sheet->setCellValueByColumnAndRow($colIndex++, $headerRow, $title);
            }
        
            // Style cho header (in đậm + căn giữa)
            $lastCol = Coordinate::stringFromColumnIndex(count($columns) + 1);
            $headerRange = "A{$headerRow}:{$lastCol}{$headerRow}";
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
            // Wrap text + auto row height
            $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($headerRow)->setRowHeight(-1); // Excel tự tính chiều cao
        }
        


        // ====== 2️⃣ Ghi dữ liệu ======
        $currentRow = $startRow;
        $stt = 1;
        $totalColumns = count($columns) + 1;
        $lastCol = Coordinate::stringFromColumnIndex($totalColumns);

        foreach ($data as $index => $item) {
            if ($index > 0) {
                $sheet->insertNewRowBefore($currentRow, 1);
            }

            // Copy style mẫu
            $sheet->duplicateStyle(
                $sheet->getStyle("A{$startRow}:{$lastCol}{$startRow}"),
                "A{$currentRow}:{$lastCol}{$currentRow}"
            );

            // Cột STT
            $colIndex = 1;
            $sheet->setCellValueExplicitByColumnAndRow($colIndex++, $currentRow, $stt++, 'n');

            foreach ($columns as $col) {
                $field = $col['field'];
                $type  = $col['type'] ?? 'string';
                $align = $col['align'] ?? null;
                $value = $item[$field] ?? ($item->$field ?? '');

                switch ($type) {
                    case 'numeric':
                        $sheet->setCellValueExplicitByColumnAndRow($colIndex, $currentRow, (float)$value, 'n');
                        break;

                    case 'date':
                        try {
                            if (!empty($value)) {
                                $excelDate = ExcelDate::PHPToExcel(Carbon::parse($value)->toDateString());
                                $sheet->setCellValueByColumnAndRow($colIndex, $currentRow, $excelDate);
                                $sheet->getStyleByColumnAndRow($colIndex, $currentRow)
                                    ->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                            }
                        } catch (\Exception) {
                            $sheet->setCellValueExplicitByColumnAndRow($colIndex, $currentRow, (string)$value, 's');
                        }
                        break;

                    default:
                        $sheet->setCellValueExplicitByColumnAndRow($colIndex, $currentRow, (string)$value, 's');
                        break;
                }

                // Align riêng từng cột
                if ($align) {
                    $alignConst = match ($align) {
                        'center' => Alignment::HORIZONTAL_CENTER,
                        'right'  => Alignment::HORIZONTAL_RIGHT,
                        default  => Alignment::HORIZONTAL_LEFT,
                    };
                    $sheet->getStyleByColumnAndRow($colIndex, $currentRow)
                        ->getAlignment()->setHorizontal($alignConst);
                }

                $colIndex++;
            }

            // Auto height
            if (!empty($options['auto_height'])) {
                $sheet->getRowDimension($currentRow)->setRowHeight(-1);
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")
                    ->getAlignment()->setWrapText(true);
            }

            $currentRow++;
        }

        // ====== 3️⃣ Auto width ======
        if (!empty($options['auto_width'])) {
            for ($i = 1; $i <= $totalColumns; $i++) {
                $colLetter = Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        // ====== 4️⃣ Định dạng số ======
        foreach ($columns as $i => $col) {
            if (($col['type'] ?? '') === 'numeric') {
                $colLetter = Coordinate::stringFromColumnIndex($i + 2);
                $sheet->getStyle("{$colLetter}{$startRow}:{$colLetter}" . ($currentRow - 1))
                    ->getNumberFormat()->setFormatCode('#,##0');
            }
        }

        // ====== 5️⃣ Fit All Columns on One Page ======
        if (!empty($options['fit_to_page'])) {
            $pageSetup = $sheet->getPageSetup();
            $pageSetup->setFitToWidth(1);
            $pageSetup->setFitToHeight(0);
            $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        }

        // ====== 6️⃣ Font vùng dữ liệu ======
        $defaultFont = ['name' => 'Times New Roman', 'size' => 12];
        $font = $options['row_font'] ?? $defaultFont;
        $range = "A{$startRow}:{$lastCol}" . ($currentRow - 1);
        $fontStyle = $sheet->getStyle($range)->getFont();

        if (!empty($font['name'])) $fontStyle->setName($font['name']);
        if (!empty($font['size'])) $fontStyle->setSize($font['size']);
        if (!empty($font['bold'])) $fontStyle->setBold(true);
        if (!empty($font['color'])) $fontStyle->getColor()->setRGB($font['color']);

       // ====== 7️⃣ Xuất file ======
        $fileName = $options['fileName'] ?? ('Bao_gia_' . now()->format('Ymd_His') . '.xlsx');
        $storageDir = 'baogia'; // Thư mục trong storage/app/public
        $filePath = "{$storageDir}/{$fileName}";

        // ✅ Tạo thư mục nếu chưa tồn tại
        \Storage::disk('public')->makeDirectory($storageDir);

        // ✅ Đường dẫn đầy đủ
        $fullPath = storage_path("app/public/{$filePath}");

        // ✅ Ghi file Excel
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($fullPath);

        // ✅ Trả về đường dẫn tương đối
        return [
            'path' => $filePath, // baogia/Bao_gia_20251029_143055.xlsx
            'name' => $fileName,
        ];

    }


    /**
     * Ghi text + style cho 1 vùng ô, dùng được cho title, subtitle, footer, ...
     */
    protected function applyTextStyle($sheet, array $option): void
    {
        $cell = $option['cell'] ?? null;
        $text = $option['text'] ?? '';
        if (!$cell) return;

        $sheet->setCellValue($cell, $text);
        $style = $option['style'] ?? [];
        $cellStyle = $sheet->getStyle($cell);

        if (!empty($style['bold'])) {
            $cellStyle->getFont()->setBold(true);
        }
        if (!empty($style['size'])) {
            $cellStyle->getFont()->setSize($style['size']);
        }
        if (!empty($style['color'])) {
            $cellStyle->getFont()->getColor()->setRGB($style['color']);
        }
        if (!empty($style['align'])) {
            $align = match ($style['align']) {
                'center' => Alignment::HORIZONTAL_CENTER,
                'right' => Alignment::HORIZONTAL_RIGHT,
                default => Alignment::HORIZONTAL_LEFT,
            };
            $cellStyle->getAlignment()->setHorizontal($align);
        }

        // Merge cell nếu có
        if (!empty($option['merge'])) {
            $sheet->mergeCells($option['merge']);
        }
    }

    
    

    protected function applyImage($sheet, array $option): void
    {
        $path = $option['path'] ?? null;
        $cell = $option['cell'] ?? null;

        if (!$path || !file_exists($path) || !$cell) return;

        $drawing = new Drawing();
        $drawing->setPath($path);
        $drawing->setCoordinates($cell);
        $drawing->setOffsetX($option['offsetX'] ?? 0);
        $drawing->setOffsetY($option['offsetY'] ?? 0);

        // ✅ Hỗ trợ inch → pixel
        $inchToPx = fn($inch) => $inch * 96;

        if (!empty($option['width_in'])) {
            $drawing->setWidth($inchToPx($option['width_in']));
        } elseif (!empty($option['width'])) {
            $drawing->setWidth($option['width']);
        }

        if (!empty($option['height_in'])) {
            $drawing->setHeight($inchToPx($option['height_in']));
        } elseif (!empty($option['height'])) {
            $drawing->setHeight($option['height']);
        }

        // 🔥 Move and size with cells
        if (method_exists($drawing, 'setResizeWithCells')) {
            $drawing->setResizeWithCells(true);
        }

        $drawing->setWorksheet($sheet);
    }

}
