<?php
  
namespace App\Http\Controllers\API;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
  
class MobileGoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
     public function verify(Request $request)
    {
        try {
            $idToken = $request->input('id_token');

            if (!$idToken) {
                return response()->json(['error' => 'Missing id_token'], 400);
            }

            $client = new \Google_Client([
                'client_id' => '323384860483-tn2g6j1h9g55bl6gn6q7hrj1cf03ebog.apps.googleusercontent.com',
            ]);

            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                return response()->json(['error' => 'Invalid ID token'], 401);
            }

            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? 'Người dùng Google';
            $googleId = $payload['sub'] ?? null;

            if (!$email || !$googleId) {
                return response()->json(['error' => 'Thiếu thông tin tài khoản Google'], 422);
            }

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'google_id' => $googleId,
                    'password' => Hash::make(uniqid()),
                    'email_verified_at' => now(),
                ]
            );

            // Tạo token (Laravel Sanctum)
            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'Đăng nhập Google thất bại',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}   