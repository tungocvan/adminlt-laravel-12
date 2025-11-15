<div class="row justify-content-center">
    <div class="login-box">
        <div class="card card-outline card-primary">
          <div class="card-header text-center">
            <a href="/" class="h1"><b>Admin</b>TNV</a>
          </div>
          <div class="card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            <form wire:submit="save">
              <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Email" wire:model.live="email">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                  </div>
                </div>
                @error('email') 
                  <span class="text-danger w-100">{{ $message }}</span> 
                @enderror

                @if (session()->has('success'))
                  <div class="alert alert-success mt-2">
                      {{ session('success') }}
                  </div>
                @endif
              </div>
              <div class="row">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                </div>
                <!-- /.col -->
              </div>
            </form>
            <p class="mt-3 mb-1">
              <a href="{{route('login')}}">Login</a>
            </p>
          </div>
          <!-- /.login-card-body -->
        </div>
      </div>
</div>