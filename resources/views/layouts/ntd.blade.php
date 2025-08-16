<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tra cứu học sinh</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
</head>
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center">
        @yield('content')
    </div>

    @livewireScripts
</body>
</html>