@php
    if (empty($item)) return;

    // Nếu có quyền "can" mà user không có => bỏ qua
    if (isset($item['can']) && !auth()->user()->can($item['can'])) return;

    $isHeader = isset($item['header']);
    $currentPath = trim(request()->path(), '/');

    // Chuẩn hoá url nội bộ (loại bỏ slash đầu)
    $normalizePath = function($val) {
        if (empty($val)) return null;
        if (Str::startsWith($val, ['http://','https://'])) return null;
        return trim($val, '/');
    };

    // Kiểm tra item có active đúng hay không
    $isItemActive = function($it) use ($currentPath, $normalizePath) {
        if (!empty($it['route']) && Route::is($it['route'])) return true;
        if (!empty($it['url'])) {
            $p = $normalizePath($it['url']);
            if ($p !== null && $currentPath === $p) return true;
        }
        if (!empty($it['href'])) {
            $p = $normalizePath($it['href']);
            if ($p !== null && $currentPath === $p) return true;
        }
        return false;
    };

    // Kiểm tra có con nào active không (dành cho menu-open)
    $hasActiveInChildren = function($children) use (&$hasActiveInChildren, $isItemActive) {
        foreach ($children as $c) {
            if ($isItemActive($c)) return true;
            if (!empty($c['submenu']) && $hasActiveInChildren($c['submenu'])) return true;
        }
        return false;
    };
@endphp

@if($isHeader)
    <li class="nav-header text-uppercase text-muted small mt-3 mb-1 px-3">
        {{ $item['header'] ?? $item['text'] ?? 'UNTITLED' }}
    </li>
@else
    @php
        // Link
        $link = '#';
        if (!empty($item['route'])) {
            try { $link = route($item['route']); } catch (\Exception $e) {}
        } elseif (!empty($item['url'])) {
            $link = url($item['url']);
        } elseif (!empty($item['href'])) {
            $link = $item['href'];
        }

        $isActive = $isItemActive($item);
        $hasActiveSub = !empty($item['submenu']) && $hasActiveInChildren($item['submenu']);
        $isOpen = $hasActiveSub; // chỉ mở nếu con active
    @endphp

    <li class="nav-item {{ !empty($item['submenu']) ? 'has-treeview' : '' }} {{ $isOpen ? 'menu-open' : '' }}">
        <a href="{{ empty($item['submenu']) ? $link : '#' }}" 
           class="nav-link {{ $isActive ? 'active' : ($hasActiveSub ? 'active-parent' : '') }}">
            @if(!empty($item['icon']))
                <i class="nav-icon {{ $item['icon'] }}"></i>
            @else
                <i class="nav-icon far fa-circle"></i>
            @endif
            <p>
                {{ $item['text'] ?? 'No Title' }}
                @if(!empty($item['submenu']))
                    <i class="right fas fa-angle-left"></i>
                @endif
            </p>
        </a>

        {{-- submenu --}}
        @if(!empty($item['submenu']))
            @php
                $visibleSubs = collect($item['submenu'])->filter(fn($sub) =>
                    !isset($sub['can']) || auth()->user()->can($sub['can'])
                );
            @endphp
            @if($visibleSubs->isNotEmpty())
                <ul class="nav nav-treeview" style="{{ $isOpen ? 'display:block;' : '' }}">
                    @foreach($visibleSubs as $subitem)
                        @php
                            $sublink = '#';
                            if (!empty($subitem['route'])) {
                                try { $sublink = route($subitem['route']); } catch (\Exception $e) {}
                            } elseif (!empty($subitem['url'])) {
                                $sublink = url($subitem['url']);
                            } elseif (!empty($subitem['href'])) {
                                $sublink = $subitem['href'];
                            }
                            $isSubActive = $isItemActive($subitem);
                        @endphp

                        <li class="nav-item">
                            <a href="{{ $sublink }}" class="nav-link {{ $isSubActive ? 'active' : '' }}">
                                @if(!empty($subitem['icon']))
                                    <i class="nav-icon {{ $subitem['icon'] }}"></i>
                                @else
                                    <i class="far fa-dot-circle nav-icon"></i>
                                @endif
                                <p>{{ $subitem['text'] ?? 'No Title' }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </li>
@endif
