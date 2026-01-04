<!-- Banner -->
<!-- Banner -->
<div class="container-fluid header-banner p-0">
    <img src="/images/banner.webp" alt="Banner">
</div>

<!-- Menu -->
<header id="menu" class="site-header bg-white">
    <div class="container">
        <div class="row align-items-center">

            <!-- Logo -->
            <div class="col-lg-2 col-md-3 col-6">
                <a href="{{ route('website.index') }}" class="logo">
                    <img src="/images/logo-tnv.png" alt="Logo">
                </a>
            </div>

            <!-- Main menu -->
            <div class="col-lg-7 d-none d-lg-flex justify-content-center">
                <nav class="main-nav">
                    <a href="{{ route('website.about') }}" @class(['active' => request()->routeIs('website.about')])>
                        Về chúng tôi
                    </a>

                    <a href="{{ route('website.products.index') }}" @class(['active' => request()->routeIs('website.products.*')])>
                        Sản phẩm
                    </a>

                    <a href="{{ route('website.help-order') }}" @class(['active' => request()->routeIs('website.help-order')])>
                        Hướng dẫn đặt hàng
                    </a>

                    <a href="{{ route('website.news') }}" @class(['active' => request()->routeIs('website.news')])>
                        Tin tức
                    </a>

                    <a href="{{ route('website.register') }}" class="highlight">
                        Đăng ký bán hàng
                    </a>
                </nav>
            </div>

            <!-- Right actions -->
            <div class="col-lg-3 col-md-9 col-6 d-flex justify-content-end align-items-center gap-3">

                {{-- 🔥 LUÔN LOAD LIVEWIRE MODAL --}}
                @livewire('website.login-form')
                @livewire('website.register-form')

                {{-- 🔁 CHỈ ĐIỀU KHIỂN MENU --}}
                @guest
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm"
                        onclick="Livewire.dispatch('openLoginModal')">
                        Đăng nhập
                    </a>
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm mx-2"
                        onclick="Livewire.dispatch('openRegisterModal')">
                        Đăng ký
                    </a>
                @else
                    @livewire('website.cart.cart-icon')

                    <div class="dropdown">
                        <a class="user-dropdown dropdown-toggle" data-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.index') }}">
                                    Admin
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endguest
            </div>

        </div>
    </div>
</header>

<style>
    /* Banner */
    .header-banner img {
        width: 100%;
        height: 70px;
        object-fit: cover;
    }

    /* Header */
    .site-header {
        height: 90px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    /* Logo */
    .logo img {
        height: 70px;
        max-width: 100%;
    }

    /* Menu */
    .main-nav {
        display: flex;
        gap: 28px;
    }

    .main-nav a {
        position: relative;
        font-weight: 500;
        color: #333;
        padding: 6px 0;
        text-decoration: none;
    }

    .main-nav a:hover {
        color: #0d6efd;
    }

    .main-nav a.active::after,
    .main-nav a:hover::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 2px;
        background: #0d6efd;
    }

    /* Highlight CTA */
    .main-nav a.highlight {
        color: #0d6efd;
        font-weight: 600;
    }

    /* User */
    .user-dropdown {
        cursor: pointer;
        font-weight: 500;
    }

    /* Sticky */
    .menu-fixed {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1050;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }
</style>

<script>
    const menu = document.getElementById('menu');
    const bannerHeight = document.querySelector('.header-banner').offsetHeight;

    window.addEventListener('scroll', () => {
        menu.classList.toggle('menu-fixed', window.scrollY > bannerHeight);
    });
</script>
