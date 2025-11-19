<div>
    @if(session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form wire:submit.prevent="login">
        <div class="form-group">
            <label>Username</label>
            <input type="text" wire:model="username" class="form-control">
        </div>

        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" wire:model="password" class="form-control">
        </div>

        <div class="form-group">
            <label>Captcha</label>
            @if(isset($captchaSvg))
                <div>{!! $captchaSvg !!}</div>
            @else
                <div>Đang tải captcha...</div>
            @endif
            <input type="text" wire:model="cvalue" class="form-control" placeholder="Nhập captcha">
        </div>
        

        <button type="submit" class="btn btn-primary mt-2">Đăng nhập</button>
    </form>

    @if(isset($token))
        <div class="alert alert-success mt-3">
            Token: <strong>{{ $token }}</strong>
        </div>
    @endif
</div>
