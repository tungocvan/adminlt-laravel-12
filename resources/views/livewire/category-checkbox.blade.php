@foreach($categories as $cat)
    <div class="form-check ml-{{ ($level ?? 0) * 3 }}">
        <input type="checkbox"
               class="form-check-input"
               value="{{ $cat->id }}"
               wire:model.live="selectedCategories"
               id="cat_{{ $cat->id }}">
        <label class="form-check-label" for="cat_{{ $cat->id }}">
            {{ str_repeat('— ', $level ?? 0) }}{{ $cat->name }}
        </label>
    </div>

    @if($cat->childrenRecursive->isNotEmpty())
        @include('livewire.category-checkbox', [
            'categories' => $cat->childrenRecursive,
            'level' => ($level ?? 0) + 1
        ])
    @endif
@endforeach

