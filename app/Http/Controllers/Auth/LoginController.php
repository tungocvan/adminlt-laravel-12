<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request){

        
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $username = $request->email;
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $username)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => 'Email không tồn tại trong hệ thống.',
                ]);
            }

            // Kiểm tra email_verified_at
            if (is_null($user->email_verified_at) && $user->is_admin != 1) {
                throw ValidationException::withMessages([
                    'email' => 'Tài khoản chưa được admin phê duyệt.',
                ]);
            }


            $username = $user->username;
        }


        if (!Auth::attempt(['username' => $username, 'password' => $request->password], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Thông tin đăng nhập không đúng.',
            ]);
        }

        return redirect()->intended('dashboard')
                        ->withSuccess('You have Successfully loggedin');

    }
}
