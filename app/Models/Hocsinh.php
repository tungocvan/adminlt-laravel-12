<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hocsinh extends Model
{
    use HasFactory;

    protected $table = 'hocsinhs';   // tên bảng

    protected $fillable = [
        'stt',
        'lop',
        'ho_va_ten',
        'ngay_sinh',
        'gioi_tinh',
        'ma_dinh_danh_hoc_sinh',
        'gvcn',
        'bao_mau',
    ];
    
}
