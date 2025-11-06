<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MedicineStock extends Model
{
    use HasFactory;

    // Tên bảng nếu khác convention
    protected $table = 'medicine_stocks';

    // Không dùng auto increment composite key
    protected $primaryKey = 'id'; // id auto-increment

    // Trường có thể gán hàng loạt
    protected $fillable = [
        'medicine_id',
        'so_lo',
        'han_dung',
        'so_luong',
        'gia_von',
        'don_gia',
        'status',
        'location',
        'notes',
    ];

    // Định dạng ngày tháng
    protected $dates = [
        'han_dung',
        'created_at',
        'updated_at',
    ];

    // Quan hệ đến bảng medicines
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }

    // Accessor: hiển thị hạn dùng theo format d/m/Y
    public function getHanDungFormattedAttribute()
    {
        return $this->han_dung ? Carbon::parse($this->han_dung)->format('d/m/Y') : null;
    }

    // Scope: chỉ lô còn tồn kho
    public function scopeAvailable($query)
    {
        return $query->where('so_luong', '>', 0)
                     ->where('status', 'available');
    }
}
