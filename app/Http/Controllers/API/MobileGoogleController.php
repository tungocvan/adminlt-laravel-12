<?php
  
namespace App\Http\Controllers\API;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
  
class MobileGoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
         // Bắt đầu OAuth
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless() // quan trọng cho mobile
            ->redirect();
    }

    // Callback sau login Google
    public function handleGoogleCallbackApp()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(str()->random(12)),
                    'email_verified_at' => now(),
                ]);
            }

            // Tạo token JWT / Sanctum
            $token = $user->createToken('google_login')->plainTextToken;

            // Redirect về app với token
            $appRedirectUri = 'myapp://auth?token=' . $token;
            return redirect($appRedirectUri);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Google login failed: ' . $e->getMessage()], 500);
        }
    }




}   