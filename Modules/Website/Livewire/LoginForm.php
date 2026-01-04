<?php

namespace Modules\Website\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginForm extends Component
{
    public string $login = ''; // email hoặc username
    public string $password = '';
    public bool $remember = false;

    protected $listeners = [
        'openLoginModal' => 'resetForm',
    ];

    /**
     * Reset form khi mở modal
     */
    public function resetForm(): void
    {
        $this->reset(['login', 'password', 'remember']);
        $this->resetErrorBag();
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Submit login
     */
    public function submit()
    {
        $this->validate();

        // Xác định login là email hay username
        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $field    => $this->login,
            'password'=> $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'login' => 'Email / Username hoặc mật khẩu không đúng.',
            ]);
        }

        session()->regenerate();

        // Đóng modal + cập nhật UI
        $this->dispatch('closeLoginModal');
        $this->dispatch('userLoggedIn');
    }

    public function render()
    {
        return view('Website::livewire.login-form');
    }
}
