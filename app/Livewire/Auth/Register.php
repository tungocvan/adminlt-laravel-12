<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Register extends Component
{
    public $name;
    public $username;
    public $email;
    public $password;
    public $confirmPassword;

    protected $rules = [
        'name'             => 'required|string|max:255',
        'email'            => 'required|email|unique:users,email',
        'username'         => 'required|string|unique:users,username',
        'password'         => 'required|min:6',
        'confirmPassword'  => 'required|same:password',
    ];

    protected $messages = [
        'name.required'        => 'Tên không được bỏ trống',
        'email.required'       => 'Email đăng nhập không được bỏ trống',
        'email.email'          => 'Email không đúng định dạng',
        'email.unique'         => 'Email này đã tồn tại',
        'password.required'    => 'Mật khẩu không được bỏ trống',
        'password.min'         => 'Mật khẩu phải có ít nhất 6 ký tự',
        'confirmPassword.same' => 'Mật khẩu xác nhận không khớp',
    ];

    public function register()
    {
        $this->generateUsername();
        $this->validate();

        $userNew = [
            'name'     => $this->name,
            'email'    => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'is_admin' => 0,
            'email_verified_at' => now()
        ];
        

        $user = User::create($userNew);
        $userRole  = Role::firstOrCreate(['name' => 'User']);
        // Gán toàn bộ permission cho Admin
        $permissions = Permission::where('name', 'admin-list')->pluck('id');
        $userRole->syncPermissions($permissions);
        $user->assignRole($userRole);
        Auth::login($user);

        return redirect()->intended('/');
    }

    public function generateUsername()
    {
        if (!$this->email) {
            return;
        }

        // Lấy phần trước @
        $base = strstr($this->email, '@', true);

        // Nếu rỗng thì fallback name
        $base = $base ?: 'user';

        $username = $base;
        $count = 0;

        // Kiểm tra trùng username trong DB
        while (User::where('username', $username)->exists()) {
            $count++;
            $username = $base . '_' . str_pad($count, 3, '0', STR_PAD_LEFT);
            if ($count > 999) {
                // Tránh vòng lặp vô tận
                break;
            }
        }

        $this->username = $username;
        //dd($this->username);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
