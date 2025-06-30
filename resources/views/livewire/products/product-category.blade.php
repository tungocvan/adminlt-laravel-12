<div class="container mt-5">
    <h2 class="mb-4">Manage Product Categories</h2>
    
    <button class="btn btn-primary mb-4" wire:click="showCreateForm">
        <i class="fas fa-plus me-2 mx-1"></i>Create New Category
    </button>

    @if($showForm)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            {{ $editingId ? 'Edit Category' : 'Add New Category' }}
        </div>
        <div class="card-body">
            <form wire:submit.prevent="createCategory">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" wire:model="name" autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label for="parentId" class="form-label">Parent Category</label>
                    <select class="form-select @error('parentId') is-invalid @enderror" 
                            id="parentId" wire:model="parentId">
                        <option value="">-- No Parent --</option>
                        @foreach($this->getParentOptions() as $category)
                            <option value="{{ $category->term_id }}">
                                {{ str_repeat('— ', $category->level) }}{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parentId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>{{ $editingId ? 'Update' : 'Save' }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary mx-2" wire:click="showCreateForm">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (session('message'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h3 class="mb-0">Existing Categories</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($categories as $category)
                    @include('livewire.products.category-item', [
                        'category' => $category,
                        'level' => 0
                    ])
                @endforeach
            </div>
        </div>
    </div>
</div>