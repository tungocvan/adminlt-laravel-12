<div class="form-check m-2">
    <input type="checkbox" 
           wire:model.defer="selectedCategories" 
           value="{{ $category->term_id }}"
           id="category_{{ $category->term_id }}" 
           class="form-check-input">
    <label class="form-check-label" for="category_{{ $category->term_id }}">
        {{ $category->name }}
    </label>
</div>

@if(!empty($category->children))
    @foreach($category->children as $child)
        <div class="ml-4">
            @include('livewire.products.partials.category', ['category' => $child])
        </div>
    @endforeach
@endif