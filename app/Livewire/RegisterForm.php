<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;

class RegisterForm extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $showModal = false;

    // Thuộc tính config modal
    public $modalTitle = 'Đăng ký tài khoản';
    public $width = 'modal-md'; // mặc định medium ; modal-sm → nhỏ; modal-lg → lớn; modal-xl → extra large

    public function mount($modalTitle = null, $width = null)
    {
     
        if ($modalTitle) {
            $this->modalTitle = $modalTitle;
        }
        if ($width) {
            $this->width = $width;
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

       public function register()
    {
     
        // ✅ Validate với rules() bên trên
        // $validated = $this->validate();

        // User::create([
        //     'name'     => $validated['name'],
        //     'email'    => $validated['email'],
        //     'username'    => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        // ]);

        // $this->reset(['name', 'email', 'password', 'password_confirmation']);

        // session()->flash('success', 'Đăng ký thành công!');

        $this->dispatch('user-registered');
        $this->closeModal(); // đóng modal
        $this->reset();      // reset input
    }

    public function openModal()
    {
        $this->reset(['name', 'email', 'password']);
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.register-form');
    }
}
