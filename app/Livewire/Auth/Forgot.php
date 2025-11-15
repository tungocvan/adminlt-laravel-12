<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Forgot extends Component
{
    public string $email = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email không được bỏ trống',
            'email.email'    => 'Email không đúng định dạng',
        ];
    }

    public function save(): void
    {
      
        $this->validate();

        // Kiểm tra email đã yêu cầu reset trong 5 phút qua chưa
        $exists = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->where('created_at', '>', Carbon::now()->subMinutes(5))
            ->exists();
            
            
        $status = Password::sendResetLink([
            'email' => $this->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.');
            $this->reset('email');
        } else {
            $this->addError('email', 'Email này không tồn tại trong hệ thống.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot');
    }
}
