<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Option;

class UserOptionController extends Controller
{
    /**
     * Cập nhật hoặc tạo thông tin userInfo vào bảng wp_options
     */
    public function updateUserInfo(Request $request)
    {
      
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'user_info' => 'required|array',
        ]);
      
        $optionName = 'user_' .(string) $validated['user_id'] . '_info';
        $optionValue = json_encode($validated['user_info'], JSON_UNESCAPED_UNICODE);

        // Sử dụng hàm tiện ích trong model Option
        $option = Option::update_option($optionName, $optionValue);

        return response()->json([
            'success' => true,
            'message' => 'User info updated successfully!',
            'data' => [
                'option_name' => $optionName,
                'option_value' => json_decode($option->option_value, true),
            ],
        ]);
    }

    /**
     * Lấy thông tin userInfo theo user_id
     */
    public function getUserInfo($user_id)
    {
        $optionName = 'user_' .(string) $user_id . '_info';
        $data = Option::get_option($optionName);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No user info found.',
            ], 404);
        }

        // Nếu là chuỗi JSON, decode lại
        $decoded = json_decode($data, true);
        return response()->json([
            'success' => true,
            'data' => $decoded ?: $data,
        ]);
    }

    /**
     * Xóa thông tin userInfo
     */
    public function deleteUserInfo($user_id)
    {
        $optionName = 'user_' . $user_id . '_info';
        $deleted = Option::delete_option($optionName);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? 'User info deleted successfully.'
                : 'User info not found or already deleted.',
        ]);
    }

    public function store(Request $request)
    {
        // Validate file upload
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // tối đa 5MB
        ]);

        // Lưu vào thư mục public/uploads
        $path = $request->file('file')->store('uploads', 'public');

        // Lấy URL công khai
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Upload thành công!',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
