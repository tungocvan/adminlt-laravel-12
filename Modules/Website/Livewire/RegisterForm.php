<?php

namespace Modules\Website\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected $listeners = [
        'openRegisterModal' => 'resetForm',
    ];

    /**
     * Reset form & error khi mở modal
     */
    public function resetForm(): void
    {
        $this->reset([
            'name',
            'email',
            'password',
            'password_confirmation',
        ]);

        $this->resetErrorBag();
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Submit register
     */
    public function submit()
    {
        if (auth()->check()) {
            return;
        }

        $validated = $this->validate();

        // Auto generate username từ email
        $username = explode('@', $validated['email'])[0];
        $baseUsername = $username;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }

        $user = User::create([
            'name' => $validated['name'] ?: $username,
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        session()->regenerate();

        // Đóng modal + reload UI
        $this->dispatch('closeRegisterModal');
        $this->dispatch('userLoggedIn');
    }

    public function render()
    {
        return view('Website::livewire.register-form');
    }
}
