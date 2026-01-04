<div class="d-inline-block">
    @if($type === 'icon')
        {{-- Icon Button --}}
        <button type="button"
                wire:click="addToCart"
                wire:loading.attr="disabled"
                class="btn btn-outline-primary {{ $size === 'sm' ? 'btn-sm' : ($size === 'lg' ? 'btn-lg' : '') }}"
                title="Thêm vào giỏ hàng">
            <span wire:loading.remove wire:target="addToCart">
                <i class="fas fa-cart-plus"></i>
            </span>
            <span wire:loading wire:target="addToCart">
                <span class="spinner-border spinner-border-sm"></span>
            </span>
        </button>
    @else
        {{-- Full Button --}}
        <button type="button"
                wire:click="addToCart"
                wire:loading.attr="disabled"
                class="btn btn-primary {{ $size === 'sm' ? 'btn-sm' : ($size === 'lg' ? 'btn-lg' : '') }} btn-block">
            <span wire:loading.remove wire:target="addToCart">
                <i class="fas fa-cart-plus mr-1"></i>
                @if($showText)
                    Thêm vào giỏ
                @endif
            </span>
            <span wire:loading wire:target="addToCart">
                <span class="spinner-border spinner-border-sm mr-1"></span>
                Đang thêm...
            </span>
        </button>
    @endif
</div>