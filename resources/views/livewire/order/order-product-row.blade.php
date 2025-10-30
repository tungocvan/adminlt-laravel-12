<tr>
    <td>
        <input type="checkbox" wire:click="toggleSelected" wire:model="selected">
    </td>
    <td>{{ $product->ten_hoat_chat }}</td>
    <td>{{ $product->don_vi_tinh }}</td>
    <td>{{ number_format($product->don_gia,0) }}</td>
    <td>
        @if($selected)
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <button type="button" class="btn btn-outline-secondary" wire:click="decrement">-</button>
            </div>
            <input type="text" class="form-control text-center" value="{{ $quantity }}" readonly>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary" wire:click="increment">+</button>
            </div>
        </div>
        @endif
    </td>
    <td>
        @if($selected)
            {{ number_format($product->don_gia * $quantity,0) }}
        @endif
    </td>
</tr>
