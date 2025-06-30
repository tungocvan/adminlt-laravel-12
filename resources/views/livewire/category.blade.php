<div class="form-group">
    <label>Categories <span class="text-danger">*</span></label>
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownCategories" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Select Categories
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownCategories">
            @foreach($categoriesTree as $category)
                @include('livewire.partials.category', ['category' => $category])
            @endforeach
        </div>
    </div>
    @error('selectedCategories') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>