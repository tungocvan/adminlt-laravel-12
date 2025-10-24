<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Option;
use App\Models\User;

class UserOptionController extends Controller
{
    
    public function getFilterUserInfo(Request $request)
    {
        // 🔹 Bắt buộc có email
        $email = $request->input('email');
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Trường email là bắt buộc.',
            ], 422);
        }

        // 🔹 Tìm user theo email
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy user với email: ' . $email,
            ], 404);
        }
     
        // 🔹 Lấy dữ liệu option user info
        $optionName = 'user_' . (string)$user->id . '_info';
        $data = \App\Models\Option::get_option($optionName);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu user info.',
            ], 404);
        }

        // 🔹 Decode JSON nếu cần
        $decoded = json_decode($data, true);
        $userInfo = is_array($decoded) ? $decoded : [];

        // 🔹 Các trường cho phép lọc
        $filters = $request->only([
            'name', 'phone', 'address', 'company', 'website', 'tax_code'
        ]);

        // 🔹 Nếu có thêm filter -> so sánh trong dữ liệu userInfo
        $filtered = [];
        $match = true;

        foreach ($filters as $key => $value) {
            if (!isset($userInfo[$key])) {
                $match = false;
                break;
            }

            // So khớp tương đối (không phân biệt hoa thường)
            if (stripos($userInfo[$key], $value) === false) {
                $match = false;
                break;
            }

            $filtered[$key] = $userInfo[$key];
        }

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Không có dữ liệu khớp với điều kiện lọc.',
            ], 404);
        }

        // 🔹 Kết quả
        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'email' => $email,
            'data' => $filtered ?: $userInfo, // nếu không có filter thì trả toàn bộ
        ]);
    }


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
