<div class="container-fluid">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="name">Tên</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên" wire:model="name"
                required>
        </div>
        {{-- <div class="form-group col-md-6">
            <x-components::tnv-input-date name="birthdate" label="Ngày sinh" wire:model.live="birthdate" placement="top"
                :config="['format' => 'DD/MM/YYYY']" />        
        </div> --}}
        <div class="form-group col-md-6">
            <label for="birthdate">Ngày sinh</label>
            <input type="date" id="birthdate" class="form-control"
                wire:model.defer="birthdate">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Nhập username"
                wire:model="username">
        </div>
        <div class="form-group col-md-6">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control" value=""
                placeholder="{{ $isEdit ? 'Để trống nếu không đổi' : 'Nhập mật khẩu' }}" wire:model="password"
                {{ $isEdit ? '' : 'required' }}>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email"
                wire:model="email" required>

        </div>

        <div class="form-group col-md-6">
            <label for="role">Role</label>
            <select id="role" name="role" class="form-control" wire:model="role">
                <option value="">-- Chọn role --</option>
                @foreach ($roles as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div> 
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="google_id">Google ID</label>
            <input type="text" id="google_id" name="google_id" class="form-control" placeholder="Nhập Google ID"
                wire:model="google_id">
        </div>
        <div class="form-group col-md-6 d-flex align-items-center">
            <div class="form-group col-md-8">
                <label for="referral_code">Mã giới thiệu</label>
                <input type="text" id="referral_code" name="referral_code" class="form-control"
                    placeholder="Mã giới thiệu" wire:model="referral_code">
            </div>
            <div class="custom-control custom-switch mt-4">
                <input type="checkbox" class="custom-control-input" id="is_admin" wire:model="is_admin">
                <label class="custom-control-label" for="is_admin">Admin</label>
            </div>
        </div>
    </div>
</div>
