<?php

use Livewire\Volt\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $usernamePreview = '';
    public bool $showModal = false;

    public string $modalTile = 'Đăng ký Thành viên';
    public string $width = 'modal-sm'; // mặc định medium modal-md; modal-sm → nhỏ; modal-lg → lớn; modal-xl → extra large
    
    

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }

    public function updatedEmail($value): void
    {
        if ($value) {
            $username = strstr($value, '@', true);
            $this->usernamePreview = $username;
        } else {
            $this->usernamePreview = '';
        }
    }

    public function openModal(): void
    {
        $this->reset(['name', 'email', 'password', 'usernamePreview']);        
        $this->showModal = true;
        
    }

    public function closeModal(): void
    {
        $this->reset(['name', 'email', 'password', 'usernamePreview']);        
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showModal = false;
    }

    public function submit(): void
    {
        $this->validate();

        // tạo username từ email
        $username = $this->usernamePreview ?: strstr($this->email, '@', true);

        // nếu trùng thì nối thêm 3 số random
        if (User::where('username', $username)->exists()) {
            $username .= rand(100, 999);
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $username,
            'password' => Hash::make($this->password),
            'is_admin' => 0,
        ]);
        $roleId = Role::where('name',['user'])->get()[0]->id ?? null; 
        $user->assignRole([$roleId]);
        session()->flash('success', 'Tạo tài khoản thành công!');
        $this->closeModal();
    }
}; ?>

<div>
    <!-- Nút mở modal -->
    <button wire:click="openModal" class="btn btn-primary" style="width:100px">
        Đăng ký
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
                                <label class="form-label">Họ tên</label>
                                <input type="text" wire:model.live="name" class="form-control">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model.live="email" class="form-control">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username (tự sinh)</label>
                                <input type="text" class="form-control" wire:model.live="usernamePreview" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu</label>
                                <input type="password" wire:model.live="password" class="form-control">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">
                                Hủy
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Tạo User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

