<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BangBaoGia;
use Illuminate\Support\Facades\Log;

class BangBaoGiaController extends Controller
{
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'product_ids'    => 'required|array|min:1',
            'product_ids.*'  => 'integer|exists:medicines,id',
            'ghi_chu'        => 'nullable|string',
        ]);

        // Sinh mã bảng giá duy nhất, ngắn gọn
        $validated['ma_so'] = 'BG-' . now()->format('ymdHis');
        $validated['user_id'] = auth()->id() ?? 1; // fallback nếu chưa login

        $bangBaoGia = BangBaoGia::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => '✅ Tạo bảng báo giá thành công!',
            'data'    => $bangBaoGia->fresh(),
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Dữ liệu không hợp lệ',
            'errors'  => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        Log::error('Lỗi tạo bảng báo giá:', ['error' => $e->getMessage()]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Không thể tạo bảng báo giá, vui lòng thử lại.',
        ], 500);
    }
}


public function index()
{
    try {
        $data = BangBaoGia::latest()->get();

        return response()->json([
            'status'  => 'success',
            'message' => $data->isEmpty() 
                ? 'Không có bảng báo giá nào.' 
                : 'Lấy danh sách bảng báo giá thành công.',
            'data'    => $data,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Lỗi lấy danh sách bảng báo giá:', ['error' => $e->getMessage()]);

        return response()->json([
            'status'  => 'error',
            'message' => 'Không thể lấy danh sách bảng báo giá.',
        ], 500);
    }
}


    public function show($id)
    {
        $bangBaoGia = BangBaoGia::findOrFail($id);
        return response()->json($bangBaoGia);
    }

    public function download($id)
    {
        $bangBaoGia = BangBaoGia::findOrFail($id);

        if (!$bangBaoGia->file_path || !file_exists(storage_path('app/public/' . $bangBaoGia->file_path))) {
            return response()->json(['message' => '❌ File không tồn tại'], 404);
        }

        return response()->download(storage_path('app/public/' . $bangBaoGia->file_path));
    }
}
