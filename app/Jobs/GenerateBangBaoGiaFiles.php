<?php

namespace App\Jobs;

use App\Models\BangBaoGia;
use App\Helpers\TnvMedicineHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class GenerateBangBaoGiaFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bangBaoGiaId;


    public $timeout = 120; // thời gian tối đa xử lý job (giây)

        
    public function __construct($bangBaoGiaId)
    {
        $this->bangBaoGiaId = $bangBaoGiaId;
    }

    public function handle()
    {
        $model = BangBaoGia::find($this->bangBaoGiaId);
       
        $pathExcel = null;
        $pathPdf = null;

        // 1️⃣ Tạo Excel
        try {
            $file = TnvMedicineHelper::exportWithTemplate([
                'selectedId'    => $model->product_ids ?? [],
                'customer_name' => $model->ten_khach_hang,
                'note'          => $model->ghi_chu ?? '',
                'company'       => $model->company ?? [],
            ]);

            if (is_array($file)) {
                $pathExcel = $file['path'] ?? null;
            } elseif (is_string($file)) {
                $pathExcel = $file;
            }
        } catch (\Throwable $e) {
            \Log::error("❌ Lỗi tạo Excel: " . $e->getMessage());
        }

        // 2️⃣ Tạo PDF
        try {
            $pdf = $model->createAdvancedPdf();
            $pathPdf = $pdf['path'] ?? null;
        } catch (\Throwable $e) {
            \Log::error("❌ Lỗi tạo PDF: " . $e->getMessage());
        }

        // 3️⃣ Cập nhật model 1 lần
        $updateData = [];
        if ($pathExcel) $updateData['file_path'] = $pathExcel;
        if ($pathPdf) $updateData['pdf_path'] = $pathPdf;
        if (!empty($updateData)) $updateData['exported_at'] = now();

        if (!empty($updateData)) {
            $model->update($updateData);
        }
    }
}
