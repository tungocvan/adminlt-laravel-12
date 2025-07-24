<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function store(Request $request)
    {
     
        // Validate dữ liệu
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'file' => 'nullable|image|max:2048', // 2MB max
            // các field khác...
        ]);

        // Xử lý file nếu có
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            $validated['file_path'] = $path;
        }

        // Lưu vào DB hoặc xử lý logic
        // Model::create($validated); // ví dụ

        return response()->json([
            'status' => 'success',
            'message' => 'Dữ liệu đã được gửi thành công!',
            'data' => $validated
        ]);
    }

}
