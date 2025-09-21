<div>
    @if(!$showForm)
        <!-- Danh sách -->
        <div class="d-flex justify-content-between mb-3">
            <input type="text" class="form-control w-25" placeholder="Tìm sản phẩm..."
                   wire:model.debounce.300ms="search">
            <button class="btn btn-primary" wire:click="create">+ Thêm sản phẩm</button>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th wire:click="$set('sortField','id')">ID</th>
                    <th>Ảnh</th>
                    <th wire:click="$set('sortField','title')">Tên</th>
                    <th>Giá</th>
                    <th>Danh mục</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
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
                        <button class="btn btn-sm btn-warning" wire:click="edit({{ $product->id }})">Sửa</button>
                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $product->id }})"
                                onclick="return confirm('Xoá sản phẩm này?')">Xoá</button>
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
                        <label>Giá thường</label>
                        <input type="number" step="0.01" class="form-control" wire:model="regular_price">
                    </div>
        
                    <div class="form-group">
                        <label>Giá khuyến mãi</label>
                        <input type="number" step="0.01" class="form-control" wire:model="sale_price">
                        @if (session('status'))
                            <div class="alert alert-danger">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group" x-data="{
                        imagePreview: null,
                     }">
                        <label>Ảnh chính</label>
                        
                        <input type="file" class="form-control mb-1"
                        wire:model="imageUpload"
                        accept="image/*"
                        @change="
                             const file = $event.target.files[0];
                             if(file){
                                 const reader = new FileReader();
                                 reader.onload = e => imagePreview = e.target.result;
                                 reader.readAsDataURL(file); 
                             } else {
                                 imagePreview = null;
                             }
                        ">
                        @if($image)
                            <div class="position-relative mr-2 mb-2 d-inline-block mt-1">
                            <img src="{{ asset('storage/'.$image) }}" width="120" class="img-thumbnail">     
                            <button type="button"
                                @click="$wire.removeImage()"
                                class="btn btn-sm btn-danger position-absolute"
                                style="top: -5px; right: -5px;">×</button>
                            </div>
                        @endif   
        
                        @if($imageUpload)
                        <template x-if="imagePreview"> 
                            <div class="position-relative d-inline-block">                         
                                <img :src="imagePreview" class="img-thumbnail" width="120">                                                             
                                <button type="button"
                                @click="imagePreview=null; $wire.removeImage()"
                                class="btn btn-sm btn-danger position-absolute"
                                style="top: -5px; right: -5px;">×</button>
                                
                            </div> 
                        </template>
                        @endif          
                        @error('imageUpload') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="form-group"
                        x-data="{
                            galleryPreview: [],
                            existingGallery: @entangle('gallery').defer 
                        }"
                    >
                        <label>Gallery (nhiều ảnh)</label>
                        <input type="file" class="form-control"
                                wire:model="galleryUpload"
                                multiple accept="image/*"
                                @change="
                                        galleryPreview = [];
                                        Array.from($event.target.files).forEach(file => {
                                            const reader = new FileReader();
                                            reader.onload = e => galleryPreview.push(e.target.result);
                                            reader.readAsDataURL(file);
                                        });
                                "
                            >
                        <div class="mt-2 d-flex flex-wrap">
                            <!-- Preview ảnh mới upload -->
                            <template x-if="galleryPreview"> 
                                <template x-for="(src, index) in galleryPreview" :key="src">
                                    <div class="position-relative mr-2 mb-2 d-inline-block">
                                        <img :src="src" class="img-thumbnail" width="100">
                                        <button type="button"
                                                @click="galleryPreview.splice(index, 1)"
                                                class="btn btn-sm btn-danger position-absolute"
                                                style="top: -5px; right: -5px; padding: 0 5px; line-height: 1;"
                                                title="Xóa ảnh">×
                                        </button>
                                    </div>
                                </template> 
                            </template>
                            @if(is_array($gallery))
                                @foreach($gallery as $key => $img)
                                <div class="position-relative mr-2 mb-2 d-inline-block">
                                    <img src="{{ asset('storage/'.$img) }}" width="100" class="mr-2 mb-2">
                                    <button type="button"
                                    @click=" $wire.removeGallery('{{ $img }}') "
                                    class="btn btn-sm btn-danger position-absolute"
                                    style="top: -5px; right: -5px; padding: 0 5px; line-height: 1;"
                                    title="Xóa ảnh">×
                                  </button>
                                </div>
                                @endforeach
                            @endif
                            
                        </div>
                    </div>
                    
        
                  
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
                <button type="button" class="btn btn-secondary ml-2" wire:click="$set('showForm', false)">Huỷ</button>
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
</script>
@endscript


