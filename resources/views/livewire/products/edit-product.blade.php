<div class="container py-4" x-data="{ showGallery: false }">
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card" x-data="formData">
        <div class="card-header">
            <h4 class="mb-0">Edit Product</h4>
        </div>
        
        <div class="card-body">
            <form wire:submit.prevent="submit" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Product Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="name" id="name" value="{{$product->post_title}}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                                       
                        <div class="form-group">                        
                            <div wire:ignore x-data="{ name: 'description' }">              
                                <x-adminlte-text-editor name="description" id="description"  label="Mô tả sản phẩm" label-class="text-danger"
                                igroup-size="sm" placeholder="Write some text..." >                              
                                    {{$product->post_content}}
                                </x-adminlte-text-editor>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror                      
                            </div>    
                        </div> 

                        <div class="form-group">                        
                            <div wire:ignore x-data="{ name: 'shortDescription' }">              
                                <x-adminlte-text-editor name="shortDescription" id="shortDescription"  label="Mô tả ngắn" label-class="text-danger"
                                igroup-size="sm" placeholder="Write some text...">
                                    {{$product->post_excerpt}}
                                </x-adminlte-text-editor>
                                @error('shortDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror                        
                            </div>    
                        </div> 
                       
                      
                    </div>

                    <div class="col-md-4">
                        

                        <div class="form-group">
                            <label for="tags">Tags</label>
                            <input type="text" wire:model.defer="tags" id="tags" class="form-control" placeholder="Separate with commas">
                            <small class="text-muted">Example: tag1, tag2, tag3</small>
                        </div>

                        <div class="form-group">
                            <label for="regularPrice">Regular Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" wire:model.defer="regularPrice" id="regularPrice" class="form-control @error('regularPrice') is-invalid @enderror" value="{{$product->_regular_price}}" required>
                            @error('regularPrice') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="salePrice">Sale Price ($)</label>
                            <input type="number" step="0.01" wire:model.defer="salePrice" id="salePrice" class="form-control @error('salePrice') is-invalid @enderror" value="{{$product->_price}}" >
                            @error('salePrice') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Main Image <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" wire:model="image" class="custom-file-input @error('image') is-invalid @enderror" id="image" accept="image/*" 
                                        x-ref="fileInput"
                                        @change="handleFile">                                           
                                        
                                <label class="custom-file-label" for="image">
                                    @if(isset($image))
                                        {{ $image->getClientOriginalName() }}
                                    @else
                                        Choose file
                                    @endif
                                </label>
                            </div>
                            @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                            @if(isset($image))
                                <div class="mt-2">                                    
                                    <img :src="previewUrl" alt="Preview" class="img-thumbnail" width="80">
                                </div>
                            @endif
                            @if(isset($product->guid))
                                <div class="mt-2">                                    
                                    <img src='/storage/{{ $product->guid }}' alt="Preview" class="img-thumbnail" width="80">
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <div x-transition class="border p-3 rounded">                             
                                <input type="file" wire:model="gallery" multiple class="form-control-file" @change="handleFileMulti">
                                <small class="text-muted">You can select multiple images</small>
                                
                                <div id="previewUrls" class="mt-2 d-flex flex-wrap">
                                   
                                    @if(isset($product->_thumbnail_id))
                                        @foreach ($product->_thumbnail_id as $url)
                                        <div class="image-wrapper position-relative me-2 mb-2">
                                            <img src='/storage/{{ $url}}' class="img-thumbnail mr-2" width="80">
                                            <button type="button" class="btn-close custom-close-btn" aria-label="Remove image"></button>
                                            </div>
                                        @endforeach
                                    @else
                                        <template x-for="url in previewUrls" :key="url">
                                            <img :src="url" class="img-thumbnail mr-2 " />
                                        </template>
                                    @endif    
                                </div>
                            </div>
                        </div>

                        {{-- <livewire:category :taxonomy="'product_cat'" /> --}}
                        <div class="form-group">
                            <label>Categories <span class="text-danger">*</span></label>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownCategories" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select Categories
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownCategories">
                                    @if(isset($categoriesTree))                                    
                                        @foreach($categoriesTree as $category)
                                            @include('livewire.products.partials.category', ['category' => $category])
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @error('selectedCategories') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>         
                    </div>
                </div>

                <div class="form-group text-right mt-4">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Product</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data("formData", () => ({
        previewUrl: null,
        previewUrls: [],        
        handleFile(event) {
              let file = event.target.files[0];
              this.fileName = file ? file.name : "Choose file";
              if (file) {
                let reader = new FileReader();
                reader.onload = (e) => this.previewUrl = e.target.result;
                reader.readAsDataURL(file);
             }
        },
        handleFileMulti(event) {
            const files = event.target.files;           
            if (files) {
                Array.from(files).forEach(file => {
                    let reader = new FileReader();                    
                    reader.onload = (e) => {
                        this.previewUrls.push(e.target.result);                    
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    }))

    document.addEventListener("DOMContentLoaded", function () {
        // Update custom file input label    
        $('.dropdown-toggle').dropdown();

    });

    document.addEventListener('livewire:initialized', () => {
        
        function createLfmButton(id) {
            return function(context) {
                let ui = $.summernote.ui;
                let button = ui.button({
                    contents: '<i class="fa fa-image"></i> LFM',
                    tooltip: "Chèn ảnh từ LFM",
                    click: function() {
                        let route_prefix = "/laravel-filemanager";
                        window.open(route_prefix + "?type=image", "FileManager", "width=900,height=600");
                        window.SetUrl = function(items) {
                            let url = items.map(item => item.url).join(",");
                            $('#' + id).summernote('insertImage', url);
                        };
                    }
                });
                return button.render();
            }
        }

      
        function editorSummerNote(id) {
            let data = '';
            let editor = $('#' + id);

            editor.summernote({   
                height:200,             
                toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['lfm','link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
                buttons: {
                    lfm: createLfmButton(id)
                }
            });

            editor.on('summernote.change', function(_, contents) {
                data = contents;
            });

            editor.on('summernote.blur.prevent', function() {
                $wire.hanlderEditor(id, data);
            });
        }


        editorSummerNote('description')
        editorSummerNote('shortDescription')

       
    })

</script>
@endscript
