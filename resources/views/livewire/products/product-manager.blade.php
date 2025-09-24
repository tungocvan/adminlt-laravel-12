<div> 
    @if(!$showForm)              
        <livewire:products.product-import />
        <div class="d-flex justify-content-between mb-3">
            <div class="input-group" style="width:50%">
                <input type="text"
                       class="form-control"
                       placeholder="Tìm sản phẩm..."
                       wire:model.live.debounce.300ms="search">
                @if($search)
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" wire:click="clearSearch">✕</button>
                    </div>
                @endif
                
                @if(count($selectedProducts) > 0)
                    <button class="btn btn-danger mx-2" wire:click="deleteSelected" 
                            onclick="return confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?')">
                        Xóa đã chọn ({{ count($selectedProducts) }})
                    </button>
                    <button wire:click="exportExcel" class="btn btn-success mx-2">
                        <i class="fa fa-file-excel"></i> Xuất Excel
                    </button>
                @endif                         

            </div>            
            <button class="btn btn-primary" wire:click="create">+ Thêm sản phẩm</button>            
            
        </div>
        <div class="d-flex mb-3">
            @if(count($selectedProducts) > 0)               
    
                <select class="form-control mr-2 w-50" wire:model.live="bulkCategory">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
    
                <button class="btn btn-success" wire:click="updateCategorySelected"
                        @disabled(!$bulkCategory)>
                    Cập nhật danh mục
                </button>
               

            @endif
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
         @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th wire:click="$set('sortField','id')">ID</th>
                    <th>Ảnh</th>
                    <th wire:click="$set('sortField','title')">Tên</th>
                    <th>Giá</th>
                    <th>Danh mục</th>
                    <th wire:click="$set('sortField','created_at')">Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $product->id }}" 
                               wire:model.live="selectedProducts">
                    </td>
                    <td>{{ $product->id }}</td>
                    <td>
                       
                        @if ($product->image == null)
                       
                           <img src="{{ asset('storage/images/default.jpg') }}" width="60">
                        @else
                             <img src="{{ asset('storage/'.$product->image ) }}" width="60">
                        @endif
                        
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>
                        @if($product->sale_price >0)
                            <span class="text-danger">{{ number_format($product->sale_price) }}</span>
                            <del>{{ number_format($product->regular_price) }}</del>
                        @else
                            {{ number_format($product->regular_price) }}
                        @endif
                    </td>
                    <td>
                        @foreach($product->categories as $cat)
                            <span class="badge badge-info">{{ $cat->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        {{ $product->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" wire:click="edit({{ $product->id }})">Sửa</button>
                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $product->id }})"
                                onclick="return confirm('Xoá sản phẩm này?')">Xoá</button>
                        <button class="btn btn-sm btn-info" wire:click="duplicate({{ $product->id }})">Nhân bản</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Không có sản phẩm</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $products->links() }}
    @else
        <!-- Form -->
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <div class="row" x-data="formData">
                <div class="col-6">
                    <div class="form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" class="form-control" wire:model="title">
                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
        
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" class="form-control" wire:model="slug">
                    </div>
        
                    <div wire:ignore  class="form-group">
                        <label>Mô tả ngắn</label>
                        <textarea id="short_description" class="form-control" wire:model="short_description"></textarea>
                    </div>
        
                    <div  wire:ignore  class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea id="description" class="form-control" rows="4" wire:model="description"></textarea>
                    </div>         
                   
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <x-money-input wire:model="regular_price" label="Giá thường" />                  
                    </div>
               
                    
        
                    <div class="form-group">
                        <x-money-input wire:model="sale_price" label="Giá khuyến mãi" />
                        @if (session('status'))
                            <div class="alert alert-danger">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                    <x-image-upload 
                        label="Ảnh chính" 
                        model="imageUpload" 
                        :current="$image" 
                        removeMethod="removeImage" 
                    />

                    <x-gallery-upload 
                        label="Gallery (nhiều ảnh)" 
                        wire:model="gallery"
                        uploadModel="galleryUpload"
                        :current="$gallery"
                        removeMethod="removeGallery" />
                    
                    <x-tag-input wire:model="tags" label="Tags (cách nhau bằng ;)" />
                  
                    <div class="form-group">
                        <label>Danh mục</label>
                        <div class="card" style="max-height: 300px; overflow-y: auto;">
                            <div class="card-body p-2">
                                {!! renderCategoryTree($categories, $selectedCategories) !!}
                            </div>
                        </div>
                        @error('selectedCategories') 
                            <small class="text-danger">{{ $message }}</small> 
                        @enderror
                    </div>
                    
                    
                </div>
            </div>
          

           

            <div class="d-flex">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary ml-2" wire:click="cancel">Huỷ</button>
            </div>
        </form>
    @endif
</div>

@script
<script type="module">
    
    Alpine.data("formData", () => ({
        init(){
            initSummernote('short_description', 200, @this);
            initSummernote('description', 300, @this);
        }
    }))

    window.addEventListener('setDescription', function(e) {
        function setContent(selector, content) {
            if ($(selector).next('.note-editor').length) {
                $(selector).summernote('code', content ?? '');
            } else {
                setTimeout(() => setContent(selector, content), 100);
            }
        }
        setContent('#description', e.detail[0].description);
        setContent('#short_description', e.detail[0].short_description);
    })

    window.addEventListener('setHeader', function(e) {
        document.getElementById('page-header').innerText = e.detail[0];
        document.title = e.detail[0]; // đổi <title>
    });
</script>
@endscript


