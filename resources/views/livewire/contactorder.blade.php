<?php

use Livewire\Volt\Component;
use App\Models\ContactOrder;

new class extends Component
{
    public string $full_name = '';
    public string $email = '';
    public string $phone = '';
    public string $user_type = '';
    public string $message = '';

    public function submit()
    {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:20',
            'user_type' => 'required|string|max:255',
            'message'   => 'nullable|string',
        ]);

        ContactOrder::create([
            'full_name' => $this->full_name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'user_type' => $this->user_type,
            'message'   => $this->message,
        ]);

        $this->reset(['full_name','email','phone','user_type','message']);
        session()->flash('success', 'Đã gửi thông tin liên hệ thành công!');
    }
}; ?>

<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit="submit">
        <div class="form-group">
            <input type="text" wire:model.live="full_name" class="form-control" placeholder="Họ Và Tên*" required>
            @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <input type="email" wire:model.live="email" class="form-control" placeholder="Email*" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <input type="tel" wire:model.live="phone" class="form-control" placeholder="Số Điện Thoại*" required>
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <select wire:model.live="user_type" class="form-control" required>
                <option value="" disabled selected>-- Bạn Là... --</option>
                <option value="Công ty Dược">Công ty Dược</option>
                <option value="Dược Sĩ">Dược Sĩ</option>
                <option value="Bác Sĩ">Bác Sĩ</option>
                <option value="Nhà Thuốc">Nhà Thuốc</option>
                <option value="Bệnh viện tư nhân">Bệnh viện tư nhân</option>                
                <option value="Phòng khám">Phòng khám</option>               
                <option value="Khác">Khác</option>
            </select>
            @error('user_type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        

        <div class="form-group">
            <textarea wire:model.live="message" class="form-control" rows="2" placeholder="Bạn Đang Tìm Sản Phẩm Gì?"></textarea>
            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success btn-block">LIÊN HỆ NGAY</button>
    </form>
</div>
