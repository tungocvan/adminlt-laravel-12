<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel 12') }}</title>
    @vite('resources/css/tailwind.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full z-10 top-0">
        <div class="max-w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Sidebar Toggle -->
                <button id="sidebarToggle" class="lg:hidden p-2 text-gray-600 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round"
                               d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <!-- Navbar Right -->
                <div class="space-x-4">
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">Home</a>
                    <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">About</a>
                    <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'text-blue-600 font-semibold' : 'text-gray-700 hover:text-blue-600' }}">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-1 pt-16">
        <!-- Sidebar -->
        <aside id="sidebar"
               class="bg-white w-64 border-r border-gray-200 hidden lg:block lg:relative fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out"
               x-data="{ openMenu: null }">
            <div class="h-full flex flex-col">
                <!-- User Panel -->
                <div class="p-4 border-b">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <p class="font-semibold">Admin</p>
                            <p class="text-sm text-gray-500">admin@example.com</p>
                        </div>
                    </div>
                </div>
                <!-- Menu -->
                <nav class="flex-1 p-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ url('/dashboard') }}"
                       class="block px-3 py-2 rounded-md {{ request()->is('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100' }}">
                        Dashboard
                    </a>

                    <!-- Users Treeview -->
                    <div>
                        <button @click="openMenu === 1 ? openMenu = null : openMenu = 1"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md 
                                {{ request()->is('users*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100' }}">
                            <span>Users</span>
                            <svg :class="openMenu === 1 ? 'rotate-90' : ''"
                                 class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="openMenu === 1 || '{{ request()->is('users*') ? 'true' : 'false' }}' === 'true'" 
                             x-collapse class="ml-6 mt-1 space-y-1">
                            <a href="{{ url('/users') }}"
                               class="block px-3 py-2 rounded-md {{ request()->is('users') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-100' }} text-sm">
                                All Users
                            </a>
                            <a href="{{ url('/users/create') }}"
                               class="block px-3 py-2 rounded-md {{ request()->is('users/create') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-100' }} text-sm">
                                Add New
                            </a>
                        </div>
                    </div>

                    <!-- Roles Treeview -->
                    <div>
                        <button @click="openMenu === 2 ? openMenu = null : openMenu = 2"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-md 
                                {{ request()->is('roles*') || request()->is('permissions*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100' }}">
                            <span>Roles & Permissions</span>
                            <svg :class="openMenu === 2 ? 'rotate-90' : ''"
                                 class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="openMenu === 2 || '{{ request()->is('roles*') || request()->is('permissions*') ? 'true' : 'false' }}' === 'true'"
                             x-collapse class="ml-6 mt-1 space-y-1">
                            <a href="{{ url('/roles') }}"
                               class="block px-3 py-2 rounded-md {{ request()->is('roles*') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-100' }} text-sm">
                                Roles
                            </a>
                            <a href="{{ url('/permissions') }}"
                               class="block px-3 py-2 rounded-md {{ request()->is('permissions*') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-100' }} text-sm">
                                Permissions
                            </a>
                        </div>
                    </div>

                    <!-- Settings -->
                    <a href="{{ url('/settings') }}"
                       class="block px-3 py-2 rounded-md {{ request()->is('settings') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-100' }}">
                        Settings
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-6 lg:ml-64">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-6">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-gray-600 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </footer>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>

</body>
</html>
