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
                    <td><img src="{{ $product->image }}" width="50"></td>
                    <td>{{ $product->title }}</td>
                    <td>
                        @if($product->sale_price)
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

            <div class="form-group">
                <label>Tên sản phẩm</label>
                <input type="text" class="form-control" wire:model="title">
                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Slug</label>
                <input type="text" class="form-control" wire:model="slug">
            </div>

            <div class="form-group">
                <label>Mô tả ngắn</label>
                <textarea class="form-control" wire:model="short_description"></textarea>
            </div>

            <div class="form-group">
                <label>Mô tả chi tiết</label>
                <textarea class="form-control" rows="4" wire:model="description"></textarea>
            </div>

            <div class="form-group">
                <label>Giá thường</label>
                <input type="number" step="0.01" class="form-control" wire:model="regular_price">
            </div>

            <div class="form-group">
                <label>Giá khuyến mãi</label>
                <input type="number" step="0.01" class="form-control" wire:model="sale_price">
            </div>

            <div class="form-group">
                <label>Ảnh chính</label>
                <input type="file" class="form-control" wire:model="imageUpload">
                @if($imageUpload)
                    <img src="{{ $imageUpload->temporaryUrl() }}" width="120" class="mt-2">                    
                @elseif($image)
                    <img src="{{ asset('storage/'.$image) }}" width="120" class="mt-2">
                @endif
                @error('imageUpload') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            
            <div class="form-group">
                <label>Gallery (nhiều ảnh)</label>
                <input type="file" class="form-control" wire:model="galleryUpload" multiple>
                <div class="mt-2 d-flex flex-wrap">
                    @foreach($galleryUpload as $file)
                        <img src="{{ $file->temporaryUrl() }}" width="100" class="mr-2 mb-2">
                    @endforeach
                    @if(is_array($gallery))
                        @foreach($gallery as $img)
                            <img src="{{ asset('storage/'.$img) }}" width="100" class="mr-2 mb-2">
                        @endforeach
                    @endif
                </div>
            </div>
            

            <div class="form-group">
                <label>Danh mục</label>
                <select multiple class="form-control" wire:model="selectedCategories">
                    @foreach($allCategories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary ml-2" wire:click="$set('showForm', false)">Huỷ</button>
            </div>
        </form>
    @endif
</div>
