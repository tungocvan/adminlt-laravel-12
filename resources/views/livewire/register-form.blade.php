<div>
    <!-- Nút mở modal -->
    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-register">
      {{ $modalTitle }}
    </button>
  
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modal-register" aria-hidden="true" data-backdrop="static" data-keyboard="false">

      <div class="modal-dialog {{ $width }} modal-dialog-scrollable">
        <div class="modal-content">
  
          <div class="modal-header">
            <h4 class="modal-title">{{ $modalTitle }}</h4>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
  
          <div class="modal-body">
            <form wire:submit.prevent="register">
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
              
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" class="form-control" wire:model.live="name">
                    @error('name') 
                        <small class="text-danger">{{ $message }}</small>                      
                    @enderror
                </div>
  
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" wire:model.live="email">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
  
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" class="form-control" wire:model.live="password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
  
                <div class="form-group">
                    <label>Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" wire:model.live="password_confirmation">
                </div>
  
                <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
            </form>
          </div>
  
        </div>
      </div>
    </div>
  </div>
  
  <!-- Script đóng modal -->
  <script>
    window.addEventListener('user-registered', function() {
        $('#modal-register').modal('hide');
        toastr.success('Đăng ký thành công!');
    });
    
   
</script>

  