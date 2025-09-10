<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class Login extends Component
{
    
    
    public $email;   
    
    public $password;
    
    protected $rules = [
        'email'    => 'required',
        'password' => 'required|min:6',
    ];
    
    protected $messages = [
        'email.required'    => 'Tài khoản đăng nhập không được bỏ trống',        
        'password.required' => 'Mật khẩu không được bỏ trống',
        'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
    ];

    public $remember=false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->to('/');
        }
    }


    public function save()
    {
        $this->validate();
        $username = $this->email;
        $password = $this->password;
       
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


        if (!Auth::attempt(['username' => $username, 'password' => $password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Thông tin đăng nhập không đúng.',
            ]);
        }

        return redirect()->intended('/'); // hoặc route bạn muốn
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
