<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    <a href="{{ url('/') }}" class="brand-link d-flex align-items-center justify-content-center py-2">
        <img src="{{ asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}"
            alt="{{ config('adminlte.logo_img_alt', 'AdminLTE Logo') }}"
            class="brand-image img-circle elevation-3 logo-icon mr-2" style="opacity:.9; width:36px; height:36px;">
        <span class="brand-text font-weight-semibold logo-text text-white">
            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
        </span>
    </a>

    <div class="sidebar">
        {{-- Sidebar Search --}}
        <div class="form-inline my-2 px-3">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Tìm kiếm menu..."
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if (config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>

                {{-- Render menu có ACL --}}
                @each('Admin::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>
</aside>

<style>
    /* ==== SIDEBAR THEME CONFIG ==== */
    :root {
        --sidebar-bg: #1f2937;
        /* nền chính (dark navy) */
        --sidebar-color: #e5e7eb;
        /* màu chữ mặc định */
        --sidebar-hover-bg: #374151;
        /* nền khi hover */
        --sidebar-active-bg: #2563eb;
        /* nền khi active */
        --sidebar-active-color: #ffffff;
        /* chữ khi active */
        --sidebar-font-size: 0.95rem;
        /* cỡ chữ mặc định */
        --sidebar-font-family: 'Poppins', 'Roboto', sans-serif;
        --sidebar-header-color: #9ca3af;
        /* màu tiêu đề header */
        --sidebar-border-color: rgba(255, 255, 255, 0.1);
        --sidebar-transition: all 0.25s ease;
    }

    /* ==== BASE STYLING ==== */
    .main-sidebar {
        background: var(--sidebar-bg) !important;
        font-family: var(--sidebar-font-family);
        font-size: var(--sidebar-font-size);
        color: var(--sidebar-color);
        transition: var(--sidebar-transition);
    }

    .main-sidebar .brand-link {
        background: rgba(0, 0, 0, 0.15);
        border-bottom: 1px solid var(--sidebar-border-color);
        text-align: center;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: .3px;
        color: #fff !important;
    }

    .main-sidebar .brand-link:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    /* ==== NAV LINKS ==== */
    .nav-sidebar>.nav-item>.nav-link {
        color: var(--sidebar-color);
        border-radius: 8px;
        margin: 2px 6px;
        padding: 8px 12px;
        transition: var(--sidebar-transition);
    }

    .nav-sidebar>.nav-item>.nav-link:hover {
        background-color: var(--sidebar-hover-bg);
        color: #fff;
    }

    .nav-sidebar>.nav-item>.nav-link.active,
    .nav-sidebar .nav-link.active {
        background-color: var(--sidebar-active-bg);
        color: var(--sidebar-active-color);
        font-weight: 600;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    }

    .nav-sidebar .nav-link i.nav-icon {
        margin-right: 8px;
        font-size: 1rem;
        width: 20px;
        text-align: center;
        opacity: .9;
    }

    .nav-sidebar .menu-open>.nav-link {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    /* ==== SUBMENU ==== */
    .nav-treeview {
        padding-left: 10px;
        border-left: 1px solid var(--sidebar-border-color);
        margin-left: 4px;
    }

    .nav-treeview .nav-item .nav-link {
        color: #cbd5e1;
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .nav-treeview .nav-link:hover {
        color: #fff;
        background-color: var(--sidebar-hover-bg);
    }

    .nav-treeview .nav-link.active {
        background-color: var(--sidebar-active-bg);
        color: var(--sidebar-active-color);
    }

    /* ==== HEADER ==== */
    .nav-header {
        color: var(--sidebar-header-color);
        font-size: 0.75rem;
        letter-spacing: 1px;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* ==== COLLAPSED SIDEBAR ==== */
    [class*="sidebar-collapse"] .main-sidebar .brand-link {
        justify-content: center !important;
    }

    [class*="sidebar-collapse"] .main-sidebar .brand-link span {
        display: none !important;
    }

    [class*="sidebar-collapse"] .nav-sidebar>.nav-item>.nav-link p {
        display: none;
    }

    [class*="sidebar-collapse"] .nav-sidebar>.nav-item>.nav-link i {
        margin-right: 0;
        text-align: center;
        width: 100%;
    }

    /* ==== SMOOTH SCROLL ==== */
    .main-sidebar .sidebar {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
    }

    .main-sidebar .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .main-sidebar .sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
    }
</style>
