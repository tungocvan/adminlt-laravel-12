<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

trait HasExcelExportTemplate
{
    /**
     * Xuất dữ liệu ra Excel từ file template có sẵn.
     */
    public function exportTemplate($templatePath, $sheetName, $data, $columns, $startRow = 10, $options = [])
    {
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        // ====== 1️⃣ Ghi Titles (có thể nhiều dòng) ======
        if (!empty($options['titles'])) {
            foreach ($options['titles'] as $titleOption) {
                $this->applyTextStyle($sheet, $titleOption);
            }
        } elseif (!empty($options['title'])) {
            // Giữ tương thích cũ
            $this->applyTextStyle($sheet, $options['title']);
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
                $type = $col['type'] ?? 'string';
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
                        'right' => Alignment::HORIZONTAL_RIGHT,
                        default => Alignment::HORIZONTAL_LEFT,
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
                    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
        }

        // ====== 6️⃣ Fit All Columns on One Page ======
        if (!empty($options['fit_to_page'])) {
            $pageSetup = $sheet->getPageSetup();
            $pageSetup->setFitToWidth(1);  // Fit toàn bộ cột trong 1 trang
            $pageSetup->setFitToHeight(0); // Không giới hạn số trang theo chiều dọc
            $pageSetup->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        }

        // ====== 5️⃣ Xuất file ======
        $fileName = 'Bao_gia_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path("app/{$fileName}");
        (new Xlsx($spreadsheet))->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
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
}
