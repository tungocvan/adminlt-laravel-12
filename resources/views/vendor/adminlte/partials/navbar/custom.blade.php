<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        @if(app()->getLocale() == 'vi')
            🇻🇳 VI
        @else
            🇺🇸 EN
        @endif
        <i class="fas fa-caret-down ml-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ route('change.lang', ['lang' => 'en']) }}" class="dropdown-item">
            🇺🇸 English
        </a>
        <a href="{{ route('change.lang', ['lang' => 'vi']) }}" class="dropdown-item">
            🇻🇳 Tiếng Việt
        </a>
    </div>
</li>
