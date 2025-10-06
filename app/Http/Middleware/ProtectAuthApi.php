<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TempToken;

class ProtectAuthApi
{
    public function handle(Request $request, Closure $next)
    {
        $excluded = [
            'api/init', // bỏ qua endpoint tạo token
        ];
        
        if (in_array($request->path(), $excluded)) {
            return $next($request);
        }
        $token = $request->header('X-Auth-Token') ?? $request->input('token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu mã token bảo vệ (X-Auth-Token)',
            ], 403);
        }

        $record = TempToken::where('token', $token)->first();

        if (!$record || now()->greaterThan($record->expires_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc đã hết hạn.',
            ], 403);
        }

        // ✅ Cho phép request đi tiếp
        $response = $next($request);

        // ✅ Xóa token sau khi đã dùng
        $record->delete();

        return $response;
    }
}
