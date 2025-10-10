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
   
         // Bắt đầu OAuth
         public function verifyGoogleIdToken(Request $request)
         {
             $idToken = $request->input('id_token');
     
             if (!$idToken) {
                 return response()->json(['error' => 'Missing id_token'], 400);
             }
     
             // Verify token với Google
             $res = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                 'id_token' => $idToken
             ]);
     
             if ($res->failed()) {
                 return response()->json(['error' => 'Invalid Google token'], 401);
             }
     
             $googleUser = $res->json();
     
             // Lấy hoặc tạo user
             $user = User::firstOrCreate(
                 ['google_id' => $googleUser['sub']],
                 [
                     'name' => $googleUser['name'],
                     'email' => $googleUser['email'],
                     'password' => Hash::make(str()->random(12)),
                     'email_verified_at' => now(),
                 ]
             );
     
             // Tạo token ứng dụng (Sanctum)
             $token = $user->createToken('google_login')->plainTextToken;
     
             return response()->json([
                 'token' => $token,
                 'user' => $user,
             ]);
         }




}   