<!-- Banner -->
<div class="container-fluid p-0">
  <img src="/images/banner.webp" class="img-fluid w-100" style="height:80px; object-fit:cover;" alt="Banner">
</div>

<!-- Menu -->
<div class="container-fluid bg-white fixed-top" style="top:80px; z-index:1030;">
  <div class="container">
    <div class="row align-items-center" style="height:72px;">
      
      <!-- Logo -->
      <div class="col-md-2 d-flex align-items-center">
        <a href="{{ route('website.index') }}">
          <img src="https://cdn-web-next.thuocsi.vn/images/logo/buymed-logo.svg" 
               alt="Logo" height="40">
        </a>
      </div>

      <!-- Menu center -->
      <div class="col-md-7 d-flex justify-content-center">
        <nav class="nav">
          <a href="{{ route('website.about') }}"
            @class(['nav-link', 'active' => request()->routeIs('website.about')])>
            Về chúng tôi
          </a>
          <a href="{{ route('website.help-order') }}"
            @class(['nav-link', 'active' => request()->routeIs('website.help-order')])>
            Hướng dẫn đặt hàng
          </a>
          <a href="{{ route('website.news') }}"
            @class(['nav-link', 'active' => request()->routeIs('website.news')])>
            Tin tức
          </a>
          <a href="{{ route('website.register') }}"
            @class(['nav-link', 'active' => request()->routeIs('website.register')])>
            Đăng ký bán hàng
          </a>
        </nav>
      </div>

      <!-- Login / Register -->
      <div class="col-md-3 d-flex justify-content-end">
        @livewire('hamada.hamada-content')
        <button class="btn btn-primary">Đăng Nhập</button>
      </div>
    </div>
  </div>
</div>
