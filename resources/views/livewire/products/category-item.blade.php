<div class="list-group-item">
    <div class="d-flex justify-content-between align-items-center py-2 px-3">
        <div class="d-flex align-items-center" style="padding-left: {{ $level * 20 }}px">
            @if($category->children->isNotEmpty())
                <button type="button" class="btn btn-sm btn-link me-2 p-0" 
                        wire:click="toggleExpand({{ $category->term_id }})"
                        style="min-width: 24px">
                    <i class="fas fa-chevron-{{ ($expanded[$category->term_id] ?? false) ? 'down' : 'right' }} text-muted"></i>
                </button>
            @else
                <span style="width: 24px; display: inline-block;"></span>
            @endif
            <span class="ms-1">{{ $category->name }}</span>
        </div>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-warning" 
                    wire:click="editCategory({{ $category->term_id }})"
                    title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-outline-danger" 
                    wire:click="deleteCategory({{ $category->term_id }})"
                    onclick="return confirm('Are you sure?') || event.stopImmediatePropagation()"
                    title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    @if(($expanded[$category->term_id] ?? false) && $category->children->isNotEmpty())
    <div class="list-group list-group-flush">
        @foreach($category->children as $child)
            @include('livewire.products.category-item', [
                'category' => $child,
                'level' => $level + 1
            ])
        @endforeach
    </div>
@endif
</div>