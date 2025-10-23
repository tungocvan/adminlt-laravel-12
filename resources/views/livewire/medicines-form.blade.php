<div>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                {{ $editMode ? 'Cập nhật thuốc' : 'Thêm thuốc mới' }}
            </h5>
        </div>

        <div class="card-body">
            {{-- Tên biệt dược --}}
            <div class="form-group">
                <label for="ten_biet_duoc">Tên biệt dược <span class="text-danger">*</span></label>
                <input type="text" id="ten_biet_duoc" class="form-control" wire:model.defer="ten_biet_duoc">
                @error('ten_biet_duoc') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Hoạt chất --}}
            <div class="form-group">
                <label for="ten_hoat_chat">Tên hoạt chất</label>
                <input type="text" id="ten_hoat_chat" class="form-control" wire:model.defer="ten_hoat_chat">
                @error('ten_hoat_chat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Dạng bào chế --}}
            <div class="form-group">
                <label for="dang_bao_che">Dạng bào chế</label>
                <input type="text" id="dang_bao_che" class="form-control" wire:model.defer="dang_bao_che">
                @error('dang_bao_che') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Đơn giá --}}
            <div class="form-group">
                <label for="don_gia">Đơn giá</label>
                <input type="number" id="don_gia" class="form-control" wire:model.defer="don_gia" step="0.01" min="0">
                @error('don_gia') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            {{-- Đơn giá --}}
            <div class="form-group">
                <label for="don_gia">Giá kê khai</label>
                <input type="number" id="don_gia" class="form-control" wire:model.defer="gia_ke_khai" step="0.01" min="0">
                @error('don_gia') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Danh mục --}}
            <div class="col-md-6">
                <div class="card card-primary collapsed-card">
                  <div class="card-header">
                    <h3 class="card-title"><label class="font-weight-bold mb-2">Danh mục thuốc</label></h3>
    
                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                    <!-- /.card-tools -->
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body" style="display: none;">
                    <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="cat-{{ $category->id }}"
                                       wire:model="selectedCategories"
                                       value="{{ $category->id }}">
                                <label class="form-check-label" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                
                            {{-- nếu bạn có danh mục con --}}
                            @if($category->children && $category->children->count())
                                @foreach($category->children as $child)
                                    <div class="form-check ml-4">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="cat-{{ $child->id }}"
                                               wire:model="selectedCategories"
                                               value="{{ $child->id }}">
                                        <label class="form-check-label" for="cat-{{ $child->id }}">
                                            — {{ $child->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                  </div>
                  <!-- /.card-body -->
                </div>
                @error('selectedCategories')
                <small class="text-danger">{{ $message }}</small>
                 @enderror
                <!-- /.card -->
              </div>
            
            
            
      
   
        

            {{-- Ghi chú / mô tả --}}
            <div class="form-group">
                <label for="ghi_chu">Ghi chú / mô tả</label>
                <textarea id="ghi_chu" rows="3" class="form-control" wire:model.defer="ghi_chu"></textarea>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" wire:click="cancel">
                <i class="fa fa-arrow-left"></i> Quay lại
            </button>

            @if($editMode)
                <button type="button" class="btn btn-success" wire:click="save">
                    <i class="fa fa-save"></i> Cập nhật
                </button>
            @else
                <button type="button" class="btn btn-primary" wire:click="store">
                    <i class="fa fa-plus-circle"></i> Thêm mới
                </button>
            @endif
        </div>
    </div>

    {{-- Loading --}}
    <div wire:loading wire:target="store,update" class="text-center mt-3">
        <div class="spinner-border text-primary" role="status"></div>
        <div>Đang xử lý...</div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        $('#categorySelect').select2();
        $('#categorySelect').on('change', function (e) {
            @this.set('selectedCategories', $(this).val());
        });
    });
</script>