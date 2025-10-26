<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'stt_tt20_2022',
        'phan_nhom_tt15',
        'ten_hoat_chat',
        'nong_do_ham_luong',
        'ten_biet_duoc',
        'dang_bao_che',
        'duong_dung',
        'don_vi_tinh',
        'quy_cach_dong_goi',
        'giay_phep_luu_hanh',
        'han_dung',
        'co_so_san_xuat',
        'nuoc_san_xuat',
        'gia_ke_khai',
        'don_gia',
        'gia_von',
        'trang_thai_trung_thau',
        'nha_phan_phoi',
        'nhom_thuoc',
        'link_hinh_anh',
        'link_hssp',
        'han_dung_visa',
        'han_dung_gmp'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_medicine')->withTimestamps();
    }

   


}
