<div class="container mt-4">
    <div class="d-flex justify-content-right mb-3">
        <h4>Quản lý Danh mục</h4>
        <button wire:click="openCreate" class="btn btn-primary mx-2">
            <i class="fa fa-plus"></i> Thêm mới
        </button>

        @if (count($selectedCategories) > 0)
            <div x-data="{
                confirmDelete() {
                    if (confirm('Bạn có chắc muốn xóa các danh mục đã chọn không?')) {
                        $wire.call('deleteAll')
                    } else {
                        // 👇 khi nhấn Hủy
                        $dispatch('delete-cancelled')
                    }
                }
            }">
                <button x-on:click="confirmDelete" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i> Xóa các lựa chọn
                </button>
            </div>
        @endif
    </div>
    <div x-data="{ showInput: false, isExport: $wire.$entangle('isExport', true) }" class="row align-items-center my-2">
        <div class="col-md-12 d-flex align-items-center flex-wrap gap-2">

            <!-- Export -->
            <template x-if="!showInput">
                <button @click="showInput = true; isExport = true" class="btn btn-success btn-sm mr-2">
                    <i class="fa fa-download"></i> Export JSON
                </button>
            </template>

            <!-- Ô nhập tên file export -->
            <template x-if="showInput">
                <div class="form-inline d-inline-flex align-items-center">
                    <input type="text" wire:model="exportFileName" placeholder="categories.json"
                        class="form-control form-control-sm mr-2 w-auto">

                    <button wire:click="exportJson" @click="showInput = false; isExport = false"
                        class="btn btn-primary btn-sm mr-1">
                        <i class="fa fa-save"></i> Lưu
                    </button>

                    <button @click="showInput = false; isExport = false" class="btn btn-secondary btn-sm">
                        Hủy
                    </button>
                </div>
            </template>

            <!-- Import (ẩn khi export đang bật) -->
            <template x-if="!isExport">
                <div class="d-flex align-items-center">
                    <!-- Nút phục hồi mặc định -->
                    <button wire:click="restoreDefault" class="btn btn-warning btn-sm mx-2">
                        <i class="fa fa-undo"></i> Phục hồi mặc định
                    </button>
                    <button wire:click="importJson" class="btn btn-info btn-sm mr-2">
                        <i class="fa fa-upload"></i> Import JSON
                    </button>
                    <input type="file" wire:model.live="importFile" accept=".json"
                        class="form-control form-control-sm w-auto">

                </div>

            </template>

            @error('importFile')
                <span class="text-danger ml-2 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="row mb-3">
        <div class="col-md-2">
            <select wire:model.live="perPage" class="form-control">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="filterType" class="form-control">
                <option value="">-- Lọc loại --</option>
                @foreach ($typeOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            {{-- <select wire:model.live="selectedParent" class="form-control">
                <option value="">-- Chọn Menu gốc --</option>
                @foreach ($parents as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select> --}}
          
            <x-components::tnv-categories wire:model.live="selectedParent" />

            {{-- <livewire:components.tnv-categories model="selectedParent"/> --}}


        </div>
        <div class="col-md-4">
            <button wire:click="$set('filterType','')" class="btn btn-sm btn-secondary"
                @if ($filterType === '') disabled @endif>
                Clear Loại
            </button>
            <button wire:click="$set('selectedParent', null)" class="btn btn-sm btn-secondary"
                @if ($selectedParent === null) disabled @endif>
                Clear Menu gốc
            </button>
        </div>
    </div>

    {{-- Chuẩn bị map prefix theo ID --}}
    @php
        $prefixMap = collect($parents)->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
    @endphp

    {{-- Table --}}
    <table class="table-bordered table-striped table">
        <thead class="thead-light">
            <tr>
                {{-- <th width="50">ID</th> --}}
                <th style="width:32px;"><input type="checkbox" wire:model.live="selectAll"></th>
                <x-sortable-column field="id" label="ID" :sortField="$sortField" :sortDirection="$sortDirection" />
                <x-sortable-column field="name" label="Tên" :sortField="$sortField" :sortDirection="$sortDirection" />
                <th>Slug</th>
                <x-sortable-column field="type" label="Loại" :sortField="$sortField" :sortDirection="$sortDirection" />
                <x-sortable-column field="is_active" label="Trạng thái" :sortField="$sortField" :sortDirection="$sortDirection" />
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>         
            {!! \App\Helpers\TnvCategoryHelper::renderCategoryRows($categories) !!}
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }}
            của {{ $categories->total() }} bản ghi
        </div>
        <div>
            {{ $categories->links('components.pagination') }}
        </div>
    </div>

    {{-- Modal Bootstrap --}}
    <div class="modal fade @if ($isModalOpen) show d-block @endif" tabindex="-1" role="dialog"
        style="background: rgba(0,0,0,0.5); @if (!$isModalOpen) display:none; @endif">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $categoryId ? 'Cập nhật Danh mục' : 'Thêm Danh mục' }}</h5>
                    <button type="button" class="close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label>Tên</label>
                            <input type="text" wire:model.live="name" class="form-control">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>Slug</label>
                            <input type="text" wire:model.live="slug" class="form-control">
                            @error('slug')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>Loại</label>
                            <select wire:model.live="type" class="form-control">
                                @foreach ($typeOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                                <option value="new">➕ Thêm loại mới...</option>
                            </select>

                            @if ($isAddingType)
                                <div class="input-group mt-2">
                                    <input type="text" wire:model.live="customType" class="form-control"
                                        placeholder="Nhập loại mới...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success btn-sm"
                                            wire:click="saveNewType">OK</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group col-md-6">
                            <label>Cha</label>
                            <select wire:model.live="parent_id" class="form-control">
                                <option value="">-- Không chọn --</option>
                                @foreach ($parents as $p)
                                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Trạng thái</label><br>
                            <input type="checkbox" wire:model.live="is_active"> Active
                        </div>

                        <div class="form-group col-md-6">
                            <label>Thứ tự</label>
                            <input type="number" wire:model.live="sort_order" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Icon</label>
                            <input type="text" wire:model.live="icon" class="form-control">
                            @error('icon')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>Can</label>
                            <input type="text" wire:model.live="can" class="form-control">
                            @error('can')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-12">
                            <label>Mô tả</label>
                            <textarea wire:model.live="description" class="form-control"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label>Ảnh</label>
                            <input type="file" wire:model.live="imageFile" class="form-control"
                                style="height:calc(2.25rem + 10px)">
                            @error('imageFile')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            @if ($image)
                                <div class="position-relative d-inline-block mt-2">
                                    <img src="{{ $image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                                        alt="" width="100" class="img-thumbnail">
                                    <button type="button" wire:click="removeImage"
                                        class="btn btn-sm btn-danger position-absolute"
                                        style="top:0; right:0; transform: translate(50%,-50%);">
                                        &times;
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary">Đóng</button>
                    <button type="button" wire:click="saveCategory" class="btn btn-primary">Lưu</button>
                </div>
            </div>
        </div>
    </div>

</div>