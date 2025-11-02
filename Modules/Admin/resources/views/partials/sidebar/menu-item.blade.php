@if(isset($item['can']))
    @can($item['can'])
        @include('Admin::partials.sidebar.menu-item-content', ['item' => $item])
    @endcan
@else
    @include('Admin::partials.sidebar.menu-item-content', ['item' => $item])
@endif
