<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    
    public function login(Request $request)
    {
        $result = tnv_login($request->only(['email', 'password']));
        $statusCode = $result['status'] === 'success' ? 201 : 400;
        return response()->json($result, $statusCode);
    }

    public function register(Request $request)
    {
        // Lấy dữ liệu từ request
        $data = $request->only([
            'name',
            'email',
            'password',
            'c_password',
            'username',
            'verified',
            'is_admin',
            'role_name'
        ]);

     
        $result = tnv_register($data);

        // Trả về response
        $statusCode = $result['status'] === 'success' ? 201 : 400;
        return response()->json($result, $statusCode);
    }
     /**
     * Tạo token tạm để bảo vệ API login/register
     */
    public function init(Request $request)
    {
        $ip = $request->ip();

        // Giới hạn 3 lần mỗi phút / 1 IP
        $recentCount = DB::table('temp_tokens')
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($recentCount >= 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn đã tạo token quá 3 lần trong 1 phút. Vui lòng thử lại sau.',
            ], 429);
        }

        // Sinh token ngẫu nhiên
        $token = Str::uuid()->toString();
        $expiresAt = Carbon::now()->addMinutes(3);

        // Lưu vào DB
        DB::table('temp_tokens')->insert([
            'token' => $token,
            'expires_at' => $expiresAt,
            'ip_address' => $ip,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo token tạm thành công!',
            'token' => $token,
            // Hiển thị giờ theo timezone hệ thống
            'expires_at' => $expiresAt->timezone(config('app.timezone'))->toDateTimeString(),
        ]);
    }
}
