<div>
    @if ($render === 'tree')
        {!! $this->renderCategoryTree($categories, $selectedCategories) !!}
    @endif

    @if ($render === 'dropdown')
        <livewire:category-dropdown :categories="$categories"  width="100%"
        wire:model.live="selectedCategories" 
        :selected="$selectedCategories" />
    @endif

</div>
