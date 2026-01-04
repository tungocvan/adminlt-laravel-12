<div>
    <button 
        wire:click="addToCart" 
        class="btn {{ $buttonClass }} {{ $attributes->get('class') }}"
        wire:loading.attr="disabled"
    >
        <span wire:loading.remove>
            <i class="fas fa-shopping-cart"></i> {{ $buttonText }}
        </span>
        <span wire:loading>
            <i class="fas fa-spinner fa-spin"></i> Đang thêm...
        </span>
    </button>
</div>
