<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Livewire Test</title>

   
    {{-- ✅ Livewire styles --}}
    @livewireStyles

    {{-- ✅ Vite --}}
    {{-- @vite(['resources/js/app.js']) --}}
</head>

<body class="hold-transition sidebar-mini">
    @yield('content')
    @livewire('test.test-list')

@yield('js')
{{-- ✅ Livewire scripts --}}
@livewireScripts

{{-- ✅ Kiểm tra Livewire hooks --}}
<script>
    console.log('js đã hiện thị');
   document.addEventListener('livewire:init', () => {
       console.log('✅ Event: livewire:init đã kích hoạt');
   
       Livewire.hook('component.initialized', component => {
           console.log('🚀 Component initialized:', component.fingerprint.name);
       });
   
       Livewire.hook('message.processed', (message, component) => {
           console.log('🔁 Component re-render:', component.fingerprint.name);
       });
   });
   </script>
</body>
</html>
