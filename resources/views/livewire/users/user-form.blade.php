{{-- user-form.blade.php --}}
<x-adminlte-modal id="modalUser" title="{{ $isEdit ? 'Cập nhật User' : 'Tạo mới User' }}" size="lg" theme="teal" icon="fas fa-user" v-centered scrollable>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
        <div class="container-fluid">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">Tên</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên" wire:model.defer="name" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email" wire:model.defer="email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Nhập username" wire:model.defer="username">
                </div>
                <div class="form-group col-md-6">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="{{ $isEdit ? 'Để trống nếu không đổi' : 'Nhập mật khẩu' }}" wire:model.defer="password" {{ $isEdit ? '' : 'required' }}>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="birthdate">Ngày sinh</label>
                    <input type="text" id="birthdate" name="birthdate" class="form-control" placeholder="dd/mm/yyyy" wire:model.defer="birthdate">
                </div>
                <div class="form-group col-md-6">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" wire:model.defer="role">
                        <option value="">-- Chọn role --</option>
                        @foreach($roles as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="google_id">Google ID</label>
                    <input type="text" id="google_id" name="google_id" class="form-control" placeholder="Nhập Google ID" wire:model.defer="google_id">
                </div>
                <div class="form-group col-md-6 d-flex align-items-center">
                    <div class="custom-control custom-switch mt-4">
                        <input type="checkbox" class="custom-control-input" id="is_admin" wire:model.defer="is_admin">
                        <label class="custom-control-label" for="is_admin">Admin</label>
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footerSlot">
            <button type="submit" class="btn btn-success">{{ $isEdit ? 'Cập nhật' : 'Lưu' }}</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
        </x-slot>
    </form>
</x-adminlte-modal>
