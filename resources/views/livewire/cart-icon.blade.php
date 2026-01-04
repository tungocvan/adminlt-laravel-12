<div>
    <a href="{{ route('website.products.cart') }}" class="btn btn-outline-success position-relative">
        <i class="fas fa-shopping-cart"></i>
        @if($cartCount > 0)
            <span class="badge badge-danger badge-pill position-absolute" 
                  style="top: -5px; right: -5px;">
                {{ $cartCount }}
            </span>
        @endif
    </a>
</div>
