<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BangBaoGia;
use Modules\Banggia\Http\Controllers\Api\BangBaoGiaController;

Route::post('/bang-gia', function (Request $request) {

    // Validate dữ liệu (khuyến nghị)
    $validated = $request->validate([
        'user_id'        => 'nullable|integer',
        'product_ids'    => 'required|array',
        'ten_khach_hang' => 'required|string',
        'ghi_chu'        => 'nullable|string',     
    ]);

    try {

        // Tạo mã số BBG
        $today = now()->format('Ymd');
        $countToday = BangBaoGia::whereDate('created_at', now())->count() + 1;

        $maSo = 'BBG_' . $today . '_' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
        $title = $request->company[0]['title'];
        $date = $request->company[0]['date'];
        // Format lại date nếu muốn dạng: TP.HCM, ngày ...
        // Nếu API truyền date sẵn (ví dụ "11/11/2025") thì lấy luôn
        $dateFormatted = 'TP.HCM, ngày ' . $date;
        $departments = $request->company[0]['departments'];
        // Lưu bảng báo giá
        $bangGia = BangBaoGia::create([
            'ma_so'          => $maSo,
            'user_id'        => $validated['user_id'] ?? Auth::id() ?? 1,
            'ten_khach_hang' => $validated['ten_khach_hang'] ?? 'QUÝ KHÁCH HÀNG',
            'ghi_chu'        => $validated['ghi_chu'] ?? '',
            'product_ids'    => $validated['product_ids'],
            'company'        => [
                'title'       => $title ?? 'BẢNG BÁO GIÁ',
                'date'        => $dateFormatted ?? $today,
                'departments' => $departments ?? 'Giám đốc Công ty',
            ],
        ]);

        // Trả kết quả JSON
        return response()->json([
            'status'  => 'success',
            'message' => "Bảng báo giá {$maSo} đã được tạo!",
            'data'    => $bangGia,
        ], 201);

    } catch (\Throwable $e) {

        Log::error('❌ Lỗi khi lưu bảng báo giá', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => '⚠️ Có lỗi xảy ra khi tạo bảng báo giá!',
        ], 500);
    }
});

Route::post('/bang-bao-gia', [BangBaoGiaController::class, 'store']);
Route::get('/bang-bao-gia', [BangBaoGiaController::class, 'index']);