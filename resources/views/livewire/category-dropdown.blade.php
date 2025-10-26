<div class="form-group" x-data="{ open: false }" style="width: {{ $width }}; position: relative;">
    <div class="btn-group w-100">
        <button type="button"
                class="btn btn-primary w-100 {{ count($selectedCategories) === 0 ? 'opacity-50' : '' }}"
                wire:click="apply"
                @disabled(count($selectedCategories) === 0)>
            @if(count($selectedCategories) === 0)
                {{ $placeholder }}
            @elseif(count($selectedCategories) <= 3)
                Áp dụng: {{ implode(', ', $selectedCategories) }}
            @else
                Áp dụng: {{ count($selectedCategories) }} danh mục đã chọn
            @endif
        </button>

        <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon"
                @click="open = !open">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
    </div>

    <div x-show="open"
         @click.away="open = false"
         class="dropdown-menu show p-3 w-100 mt-1 shadow-sm border rounded"
         style="display: block; max-height: {{ $maxHeight }}; overflow-y: auto; position: absolute; left: 0; z-index: 1050;">
        @include('livewire.category-checkbox', ['categories' => $categories])
    </div>
</div>
