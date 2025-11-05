<div>
    <!-- Nút mở modal -->
    <button class="btn btn-primary" wire:click="openModal">
      {{ $modalTitle }}
    </button>
  
    <!-- Modal -->
     <div 
        class="modal fade @if($showModal) show d-block @endif" 
        tabindex="-1" 
        role="dialog" 
        @if($showModal) style="background: rgba(0,0,0,0.5);" @endif
    >


      <div class="modal-dialog {{ $width }} modal-dialog-scrollable">
        <div class="modal-content">
  
          <div class="modal-header">
            <h4 class="modal-title">{{ $modalTitle }}</h4>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
  
          <div class="modal-body">
            <form wire:submit="register">
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
  


  <script>
    window.addEventListener('user-registered', function() {
        var modalEl = document.getElementById('modal-register');
        if (modalEl) {
            // Bootstrap 4: dùng jQuery API
            $('#modal-register').modal('hide');
            toastr.success('Đăng ký thành công!');
        }
       
    });

</script>