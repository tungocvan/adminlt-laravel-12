<div class="card mt-3">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-pills mr-2"></i>Phần mở rộng</h5>
    </div>

    <div class="card-body">
        <div class="row">
            {{-- Nhà phân phối --}}
            <div class="form-group col-md-6">
                <label for="nha_phan_phoi">Nhà phân phối</label>
                <input type="text" wire:model="nha_phan_phoi" class="form-control" id="nha_phan_phoi" placeholder="Nhập tên nhà phân phối">
                @error('nha_phan_phoi') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Nhóm thuốc --}}
            <div class="form-group col-md-6">
                <label for="nhom_thuoc">Nhóm thuốc</label>
                <input type="text" wire:model="nhom_thuoc" class="form-control" id="nhom_thuoc" placeholder="Nhập nhóm thuốc">
                @error('nhom_thuoc') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Hình ảnh thuốc --}}
            <div class="form-group col-md-12">
                <label>Hình ảnh thuốc</label>

                {{-- Nếu đã có ảnh lưu --}}
                @if ($link_hinh_anh)
                    <div class="position-relative d-inline-block mb-3">
                        <img 
                            src="{{ Storage::url($link_hinh_anh) }}" 
                            alt="Ảnh thuốc" 
                            class="img-thumbnail" 
                            style="width: 120px; height: 120px; object-fit: cover;"
                        >
                        <button 
                            type="button"
                            wire:click="removeImage"
                            class="btn btn-sm btn-danger position-absolute"
                            style="top: -8px; right: -8px;"
                            title="Xóa ảnh"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                {{-- Input upload --}}
                <div class="custom-file">
                    <input 
                        type="file" 
                        wire:model.live="imageUpload" 
                        accept="image/*"
                        class="custom-file-input" 
                        id="imageUpload"
                    >
                    <label class="custom-file-label" for="imageUpload">
                        @if ($imageUpload)
                            {{ $imageUpload->getClientOriginalName() }}
                        @else
                            Chọn hình ảnh...
                        @endif
                    </label>
                </div>

                {{-- Preview ảnh mới chọn --}}
                @if ($imageUpload)
                    <div class="mt-3">
                        <p class="text-muted mb-1"><small>Xem trước hình ảnh mới:</small></p>
                        <img src="{{ $imageUpload->temporaryUrl() }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                @endif

                @error('imageUpload') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
            </div>

            {{-- Các trường khác trong bảng Medicines --}}
            <div class="form-group col-md-6">
                <label for="co_so_san_xuat">Cơ sở sản xuất</label>
                <input type="text" wire:model="co_so_san_xuat" id="co_so_san_xuat" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="nuoc_san_xuat">Nước sản xuất</label>
                <input type="text" wire:model="nuoc_san_xuat" id="nuoc_san_xuat" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="giay_phep_luu_hanh">Giấy phép lưu hành</label>
                <input type="text" wire:model="giay_phep_luu_hanh" id="giay_phep_luu_hanh" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="han_dung">Hạn dùng</label>
                <input type="text" wire:model="han_dung" id="han_dung" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="gia_ke_khai">Giá kê khai</label>
                <input type="number" wire:model="gia_ke_khai" id="gia_ke_khai" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="don_gia">Đơn giá</label>
                <input type="number" wire:model="don_gia" id="don_gia" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="gia_von">Giá vốn</label>
                <input type="number" wire:model="gia_von" id="gia_von" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label for="trang_thai_trung_thau">Trạng thái trúng thầu</label>
                <select wire:model="trang_thai_trung_thau" id="trang_thai_trung_thau" class="form-control">
                    <option value="0">Chưa trúng thầu</option>
                    <option value="1">Đã trúng thầu</option>
                </select>
            </div>
        </div>
    </div>
</div>
