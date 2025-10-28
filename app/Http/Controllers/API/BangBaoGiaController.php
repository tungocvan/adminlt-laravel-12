<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BangBaoGia;

class BangBaoGiaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'product_ids'    => 'required|array|min:1',
            'product_ids.*'  => 'integer|exists:medicines,id',
            'ghi_chu'        => 'nullable|string',
        ]);

        // Tạo mã bảng giá ngắn gọn
        $validated['ma_so'] = 'BG-' . now()->format('ymdHis');
        $validated['user_id'] = auth()->id() ?? null;

        $bangBaoGia = BangBaoGia::create($validated);

        return response()->json([
            'message' => '✅ Tạo bảng báo giá thành công!',
            'data' => $bangBaoGia->fresh(),
        ]);
    }

    public function index()
    {
        return response()->json(BangBaoGia::latest()->get());
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
