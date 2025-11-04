{{-- Modal Add / Edit --}}
<div class="modal fade @if($showModal) show @endif" style="display:@if($showModal)block @else none @endif;" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">
                  <i class="fas fa-user-edit mr-2"></i>
                  {{ $isEdit ? 'Cập nhật người dùng' : 'Thêm người dùng mới' }}
              </h5>
              <button type="button" class="close text-white" wire:click="closeModal">
                  <span>&times;</span>
              </button>
          </div>

          <div class="modal-body">
              <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label>Tên</label>
                          <input type="text" wire:model.defer="name" class="form-control" placeholder="Nhập tên...">
                          @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>

                      <div class="form-group col-md-6">
                          <label>Email</label>
                          <input type="email" wire:model.defer="email" class="form-control" placeholder="Email...">
                          @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>
                  </div>

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label>Tên đăng nhập</label>
                          <input type="text" {{ $isEdit ? 'disabled' : '' }} wire:model.defer="username" class="form-control" placeholder="Username...">
                          @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>

                      <div class="form-group col-md-6">
                          <label>Mật khẩu</label>
                          <input type="password" wire:model.defer="password" class="form-control" placeholder="{{ $isEdit ? 'Để trống nếu không đổi' : 'Nhập mật khẩu...' }}">
                          @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                      </div>
                  </div>

                  <div class="form-row">
                      <div class="form-group col-md-6">
                          <label>Vai trò (Role)</label>
                          <select wire:model="role" class="form-control">
                              @foreach($this->roles as $value => $label)
                                  <option value="{{ $value }}">{{ $label }}</option>
                              @endforeach
                          </select>
                      </div>

                      <div class="form-group col-md-6">
                          <label>Ngày sinh</label>
                          <input type="date" wire:model.defer="birthdate" class="form-control">
                      </div>
                  </div>

                  <div class="form-group">
                      <label>Google ID (nếu có)</label>
                      <input type="text" wire:model.defer="google_id" class="form-control" placeholder="Nhập Google ID...">
                  </div>

                  <div class="form-group text-right">
                      <button type="button" class="btn btn-secondary" wire:click="closeModal">Hủy</button>
                      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Lưu' }}</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
