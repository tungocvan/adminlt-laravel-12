<div class="row justify-content-center">
    <div class="register-box">
        <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1"><b>Admin</b>TNV</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Register a new membership</p>
    
            <form wire:submit="register">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Full name" wire:model="name"> 
                <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
                </div>
                @error('name') <span class="error w-100" style="color:red;font-size:12px"> {{ $message }}</span> @enderror
            </div>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Email" wire:model="email">
                <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
                </div>
                @error('email') <span class="error w-100" style="color:red;font-size:12px"> {{ $message }}</span> @enderror
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Password" wire:model="password">
                <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
                </div>
                @error('password') <span class="error w-100" style="color:red;font-size:12px"> {{ $message }}</span> @enderror
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Retype password" wire:model="confirmPassword">
                <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
                </div>
                @error('confirmPassword') <span class="error w-100" style="color:red;font-size:12px"> {{ $message }}</span> @enderror
            </div>
            <div class="row">
                <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                    <label for="agreeTerms">
                    I agree to the <a href="#">terms</a>
                    </label>
                </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
                <!-- /.col -->
            </div>
            </form>
    
      
    
            <a href="{{route('auth.login')}}" class="text-center">I already have a membership</a>
        </div>
        <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
</div>
