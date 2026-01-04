<a href="{{ route('website.cart.index') }}" class="nav-link position-relative">
    <i class="fas fa-shopping-cart"></i>
    <span class="d-none d-md-inline ml-1">Giỏ hàng</span>
    
    @if($this->itemCount > 0)
        <span class="badge badge-danger position-absolute" 
              style="top: 0; right: -5px; font-size: 0.65rem; padding: 3px 6px;">
            {{ $this->itemCount > 99 ? '99+' : $this->itemCount }}
        </span>
    @endif
</a>