<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\TnvMedicineHelper;
use App\Models\User;
use Modules\Medicine\Models\Medicine;
use App\Traits\HasAdvancedPdfExport;
use App\Jobs\GenerateBangBaoGiaFiles;

class BangBaoGia extends Model
{
    use HasFactory, HasAdvancedPdfExport;

    protected $table = 'bang_bao_gia';

    protected $fillable = ['ma_so', 'user_id', 'ten_khach_hang', 'product_ids', 'ghi_chu', 'file_path', 'pdf_path', 'exported_at', 'company'];

    protected $casts = [
        'product_ids' => 'array',
        'company' => 'array',
        'exported_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tạo PDF nâng cao từ model
     */
    public function createAdvancedPdf()
    {
        // 🔹 Lấy dữ liệu sản phẩm đầy đủ từ DB
        $productsData = collect($this->product_ids)
            ->map(function ($id) {
                $medicine = \Modules\Medicine\Models\Medicine::find($id);
                return [
                    'item' => $medicine, // trả về toàn bộ model
                    'quantity' => 1, // nếu bạn có quantity riêng thì thay đổi ở đây
                ];
            })
            ->toArray();
        // \Log::info('✅ DEBUG var company:', [
        //     'company' => $this->company,
        //     'id' => $this->id,
        // ]);

        return $this->exportAdvancedPdf([
            'view' => 'pdf.bang_bao_gia',
            'data' => [
                'customer_name' => $this->ten_khach_hang,
                'note' => $this->ghi_chu,
                'ma_so' => $this->ma_so,
                'products' => $productsData,
                'company' => $this->company ?? [],
            ],
            'fileName' => 'BangBaoGia_' . $this->id . '.pdf',
            'footerText' => 'Cảm ơn quý khách!',
        ]);
    }

    /**
     * Booted event: tạo Job Queue khi created
     */
    protected static function booted()
    {
        static::created(function ($model) {
            // Dispatch job tạo Excel + PDF
            GenerateBangBaoGiaFiles::dispatch($model->id);
        });
    }
}
