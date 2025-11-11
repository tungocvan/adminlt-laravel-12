<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

trait HasAdvancedPdfExport
{
    /**
     * Xuất dữ liệu ra PDF nâng cao từ Blade template
     *
     * @param array $options
     * [
     *   'view' => 'pdf.bang_bao_gia',
     *   'data' => [...],
     *   'fileName' => 'Bao_gia_20251111.pdf',
     *   'storageDir' => 'baogia/pdf',
     *   'paper' => 'A4',
     *   'orientation' => 'portrait',
     *   'title' => 'BÁO GIÁ',
     *   'footerText' => 'Cảm ơn quý khách!',
     * ]
     *
     * @return array|null ['path' => ..., 'name' => ...]
     */
     public function exportAdvancedPdf(array $options)
        {
            $view = $options['view'] ?? null;
            $data = $options['data'] ?? [];
            $fileName = $options['fileName'] ?? ('Bao_gia_' . now()->format('Ymd_His') . '.pdf');
            $storageDir = $options['storageDir'] ?? 'baogia/pdf';
            $paper = $options['paper'] ?? 'A4';
            $orientation = $options['orientation'] ?? 'landscape'; // ⚡ dùng ngang
            $title = $options['title'] ?? 'BÁO GIÁ';
            $footerText = $options['footerText'] ?? '';

            if (!$view) {
                abort(400, "Chưa chỉ định Blade view để render PDF.");
            }

            \Storage::disk('public')->makeDirectory($storageDir);

            $filePath = "{$storageDir}/{$fileName}";
            $fullPath = storage_path("app/public/{$filePath}");

            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, array_merge($data, [
                    'title' => $title,
                    'footerText' => $footerText,
                ]))
                ->setPaper($paper, $orientation)
                ->setOption('isRemoteEnabled', true);

                // Footer với page number
                $pdf->setOption('footer-html', view('pdf.partials.footer', [
                    'footerText' => $footerText,
                ])->render());

                $pdf->save($fullPath);
            } catch (\Throwable $th) {
                \Log::error('❌ Lỗi tạo PDF nâng cao: ' . $th->getMessage());
                return null;
            }

            return [
                'path' => $filePath,
                'name' => $fileName,
            ];
        }

}
