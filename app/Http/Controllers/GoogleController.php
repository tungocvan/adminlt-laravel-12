<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
  
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();


    }
          
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        if ($user) {
            if (is_null($user->email_verified_at) && $user->is_admin != 1) {
                // Redirect về login với lỗi thay vì throw
                return redirect()->route('login')
                    ->withErrors(['email' => "Tài khoản $googleUser->email chưa được admin phê duyệt."]);
            }

            Auth::login($user);
            return redirect()->intended('admin');
        }

        // Nếu user chưa tồn tại → tạo mới với email_verified_at = null
        $newUser = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'username' => $googleUser->email,
            'google_id' => $googleUser->id,
            'password' => Hash::make('123456'),
            'email_verified_at' => null, // Chưa được duyệt
            'role' => 'user',
        ]);

        $newUser->assignRole('user');

        // Redirect về login với thông báo chờ duyệt
        return redirect()->route('login')
            ->withErrors(['email' => 'Tài khoản mới đã được tạo, vui lòng chờ admin phê duyệt.']);

    } catch (Exception $e) {
        // Lỗi khác, redirect về login với message
        return redirect()->route('login')
            ->withErrors(['email' => 'Đăng nhập Google thất bại: '.$e->getMessage()]);
    }
}


}   