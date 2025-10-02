<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\ContactSeller;

new class extends Component {
    use WithFileUploads;

    public string $user_type = '';
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public array $files = []; // ✅ dùng array để upload nhiều file
    

    public function rules(): array
    {
        return [
            'user_type' => 'required|string',
            'name'      => 'required|string|min:3',
            'email'     => 'required|email',
            'subject'   => 'required|string|min:3',
            'message'   => 'required|string|min:5',
            'files.*'   => 'nullable|file|max:2048', // validate từng file
        ];
    }

    public function send()
    {
        $this->validate();

        $paths = [];
        foreach ($this->files as $file) {
            $paths[] = $file->store('contacts', 'public');
        }

        ContactSeller::create([
            'user_type' => $this->user_type,
            'name'      => $this->name,
            'email'     => $this->email,
            'subject'   => $this->subject,
            'message'   => $this->message,
            'files'     => json_encode($paths), // lưu mảng file
        ]);

        $this->reset(['user_type','name','email','subject','message','files']);
        session()->flash('success', '✅ Gửi liên hệ bán hàng thành công!');
    }
}; ?>


<section class="content mt-2">
    <!-- Default box -->
    <div class="card">
        <div class="card-body row">
            <div class="col-5 text-center d-flex align-items-center justify-content-center">
                <div class="">
                    <h2>Đăng ký bán hàng</h2>
                    <p class="lead mb-5">
                        36 Nguyễn Minh Hoàng, Phường Bảy Hiền, TP.HCM<br />
                        Phone: +84 903 971 949
                    </p>
                </div>
            </div>
            
            <div class="col-7">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
            
                <form wire:submit.prevent="send" enctype="multipart/form-data">
                    <div class="form-group">
                        <select wire:model="user_type" class="form-control" required>
                            <option value="" disabled>-- Bạn Là... --</option>
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
                        <label for="inputName">Name</label>
                        <input wire:model.defer="name" type="text" id="inputName" class="form-control" />
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label for="inputEmail">E-Mail</label>
                        <input wire:model.defer="email" type="email" id="inputEmail" class="form-control" />
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label for="inputSubject">Subject</label>
                        <input wire:model.defer="subject" type="text" id="inputSubject" class="form-control" />
                        @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label for="inputMessage">Message</label>
                        <textarea wire:model.defer="message" id="inputMessage" class="form-control" rows="4"></textarea>
                        @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label for="inputFile">Danh mục sản phẩm</label>
                        <input wire:model="files" type="file" id="inputFile" class="form-control" multiple  />
                        @error('files') <small class="text-danger">{{ $message }}</small> @enderror
            
                        @if (!empty($files))
                            <small class="text-success">Đã chọn:
                                @foreach ($files as $f)
                                    {{ $f->getClientOriginalName() }}@if (!$loop->last), @endif
                                @endforeach
                            </small>
                        @endif

                    </div>
            
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Send message" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


