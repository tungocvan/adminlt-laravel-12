<?php

use Livewire\Volt\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showModal = false;

    public string $modalTile = 'Đăng nhập hệ thống';
    public string $width = 'modal-sm';

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ];
    }

    public function openModal(): void
    {
        $this->reset(['email', 'password', 'remember']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->reset(['email', 'password', 'remember']);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = false;
    }

    public function submit(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->closeModal();
            session()->flash('success', 'Đăng nhập thành công!');
            $this->redirect('/website'); // Không cần return
            return; // chỉ thoát hàm
        }

        $this->addError('email', 'Email hoặc mật khẩu không đúng.');
    }

};?>

<div>
    <!-- Nút mở modal -->
    <button wire:click="openModal" class="btn btn-primary mx-2" style="width:100px">
        Đăng nhập
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,.5);" tabindex="-1">
            <div class="modal-dialog {{ $width }} modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit="submit">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $modalTile }}</h5>
                            <button type="button" class="close" wire:click="closeModal"> <span aria-hidden="true">&times;</span></button>
                            
                        </div>

                        
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model.live="email" class="form-control">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" wire:model.live="password" class="form-control">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" wire:model.live="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                            </div>
                        </div>

                        <div class="modal-footer">                           
                            <button type="submit" class="btn btn-primary">
                                Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

