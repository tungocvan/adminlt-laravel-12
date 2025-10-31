<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\TnvMedicineHelper;
use App\Models\User;

class BangBaoGia extends Model
{ 
    use HasFactory;

    protected $table = 'bang_bao_gia'; // hoặc 'bang_bao_gias' nếu bạn đặt theo Laravel convention

    protected $fillable = [
        'ma_so',
        'user_id',
        'ten_khach_hang',
        'product_ids',
        'ghi_chu',
        'file_path',
        'exported_at',
        'company'
    ];

    protected $casts = [
        'product_ids' => 'array',
        'company' => 'array',
        'exported_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 Khi tạo bảng báo giá mới => tự động export file Excel
    protected static function booted()
    {
       
        static::created(function ($model) {
            try {
                
                $file = TnvMedicineHelper::exportWithTemplate([
                    'selectedId'    => $model->product_ids ?? [],
                    'customer_name' => $model->ten_khach_hang,
                    'note'          => $model->ghi_chu,
                    'company'       => $model->company ?? []
                ]);

                $path = null;
                if (is_array($file) && isset($file['path'])) {
                    $path = $file['path'];
                } elseif (is_string($file)) {
                    $path = $file;
                }

                if ($path) {
                    $model->update([
                        'file_path'   => $path,
                        'exported_at' => now(),
                    ]);
                }
            } catch (\Throwable $th) {
                \Log::error('❌ Lỗi tạo file báo giá tự động: ' . $th->getMessage());
            }
        });
    }

}
