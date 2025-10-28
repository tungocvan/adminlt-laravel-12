<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class GoogleAuthController extends Controller
{
    public function callback(Request $request)
    {
       
        $idToken = $request->input('id_token');

        // 1️⃣ Xác minh token với Google
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken
        ]);

        if (!$response->ok()) {
            return response()->json(['success' => false, 'message' => 'Token không hợp lệ'], 401);
        }

        $googleUser = $response->json();

        // 2️⃣ Kiểm tra email xác thực
        if (!isset($googleUser['email_verified']) || $googleUser['email_verified'] !== 'true') {
            return response()->json(['success' => false, 'message' => 'Email chưa xác thực'], 403);
        }

        // 3️⃣ Tạo hoặc cập nhật user
        $user = User::updateOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'] ?? $googleUser['email'],
                'google_id' => $googleUser['sub'],
                'avatar' => $googleUser['picture'] ?? null,
            ]
        );

        // 4️⃣ Sinh token Laravel
        $token = $user->createToken('google-login')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}
