<!-- Banner -->
<div class="container-fluid p-0">
  <img src="/images/banner.webp" class="img-fluid w-100" 
       style="height:80px; object-fit:cover;" alt="Banner">
</div>

<!-- Menu -->
<div id="menu" class="container-fluid bg-white">
  <div class="container">
    <div class="row align-items-center" style="height:128px;">
      
      <!-- Logo -->
      <div class="col-md-2 d-flex align-items-center">
        <a href="{{ route('website.index') }}">
          <img src="/images/logo-tnv.png" 
               alt="Logo" height="128">
        </a>
      </div>

      <!-- Menu center -->
      <div class="col-md-7 d-flex justify-content-center">
        <nav class="nav">
          <a href="{{ route('website.about') }}"
             @class(['nav-link', 'active' => request()->routeIs('website.about')])>
            Về chúng tôi
          </a>
          <a href="{{ route('website.products.index') }}"
             @class(['nav-link', 'active' => request()->routeIs('website.products.index')])>
            Sản phẩm
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
        @guest
          <livewire:register-form modalTitle="Đăng ký tài khoản" width="modal-md" />            
          {{-- <livewire:hamada.loginform modalTitle="Đăng nhập" width="modal-md" />   --}}
        @else
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('admin.index') }}">
                    {{ __('Admin') }}
                </a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
        </ul>
        @endguest
      </div>
    </div>
  </div>
</div>

<style>
  .nav .nav-link {
            position: relative;
            padding-bottom: 5px; /* tạo khoảng cách cho gạch */
  }

  .nav .nav-link.active::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;               /* độ dày gạch */
      background-color: #007bff; /* màu gạch */
  }
  .menu-fixed {
    position: fixed;
    top: 0; /* dính sát trên */
    left: 0;
    width: 100%;
    z-index: 1030;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: top 0.3s;
  }
</style>

<script>
  const menu = document.getElementById('menu');
  const bannerHeight = 80; // chiều cao banner

  window.addEventListener('scroll', function () {
    if (window.scrollY > bannerHeight) {
      menu.classList.add('menu-fixed');
    } else {
      menu.classList.remove('menu-fixed');
    }
  });
</script>
