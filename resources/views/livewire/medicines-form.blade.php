<div>
    {{-- NAV TAB --}}
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" data-toggle="tab" href="#general" role="tab">
                <i class="fas fa-capsules mr-1"></i> Thông tin chính
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'extend' ? 'active' : '' }}" data-toggle="tab" href="#extend" role="tab">
                <i class="fas fa-layer-group mr-1"></i> Phần mở rộng
            </a>
        </li>
    </ul>

    {{-- TAB CONTENT --}}
    <div class="tab-content mt-3">
        {{-- TAB CHÍNH --}}
        <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="general" role="tabpanel">
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tên biệt dược</label>
                                <input type="text" wire:model.defer="ten_biet_duoc" class="form-control" placeholder="Nhập tên biệt dược...">
                                @error('ten_biet_duoc') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
        
                            <div class="form-group">
                                <label>Tên hoạt chất</label>
                                <input type="text" wire:model.defer="ten_hoat_chat" class="form-control" placeholder="Nhập tên hoạt chất...">
                            </div>
        
                            <div class="row">
                                <div class="col-5">
                                    <div class="form-group">
                                        <label>Nồng độ / Hàm lượng</label>
                                        <input type="text" wire:model.defer="nong_do_ham_luong" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Đơn giá</label>
                                        <input type="number" wire:model.defer="don_gia" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Đơn vị tính</label>
                                        <input type="text" wire:model.defer="don_vi_tinh" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Trạng thái trúng thầu</label>
                                        <div class="custom-control custom-switch">
                                            <input {{ $trang_thai_trung_thau ? 'checked' : '' }} type="checkbox" class="custom-control-input" id="switchTrungThau" wire:model.live="trang_thai_trung_thau">
                                            <label class="custom-control-label" for="switchTrungThau">
                                                {{ $trang_thai_trung_thau ? 'Đã trúng thầu' : 'Chưa trúng thầu' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <x-image-upload 
                                    label="Hình ảnh thuốc"
                                    model="image"
                                    :current="$link_hinh_anh"
                                    removeMethod="removeImage"
                                     />
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Danh mục thuốc</label>                    
                                {!! renderCategoryTree($categories, $selectedCategories) !!}
                             </div>
                        </div>
                    </div>
                  
                </div>
            </div>
            
        </div>

        {{-- TAB PHẦN MỞ RỘNG --}}
        <div class="tab-pane fade {{ $activeTab === 'extend' ? 'show active' : '' }}" id="extend" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Giá kê khai</label>
                            <input type="number" wire:model.defer="gia_ke_khai" class="form-control">
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label>Giá vốn</label>
                            <input type="number" wire:model.defer="gia_von" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Số TT20/2022</label>
                            <input type="number" wire:model.defer="stt_tt20_2022" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Phân nhóm TT15</label>
                            <input type="text" wire:model.defer="phan_nhom_tt15" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">     
                        <div class="form-group col-md-3">
                            <label>Dạng bào chế</label>
                            <input type="text" wire:model.defer="dang_bao_che" class="form-control">
                        </div>
    
                        <div class="form-group col-md-3">
                            <label>Đường dùng</label>
                            <input type="text" wire:model.defer="duong_dung" class="form-control">
                        </div>      
                        <div class="form-group col-md-3">
                            <label>Quy cách đóng gói</label>
                            <input type="text" wire:model.defer="quy_cach_dong_goi" class="form-control">
                        </div>           
                        
                        <div class="form-group col-md-3">
                            <label>Hạn dùng sản phẩm</label>
                            <input type="text" wire:model.defer="han_dung" class="form-control">
                        </div>   
                    </div>

                    

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Giấy phép lưu hành</label>
                            <input type="text" wire:model.defer="giay_phep_luu_hanh" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Hạn dùng Giấy phép lưu hành</label>
                            <input type="text" wire:model.defer="han_dung_visa" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nhóm thuốc</label>
                            <input type="text" wire:model.defer="nhom_thuoc" class="form-control">
                        </div>                  
                    </div>

                    <div class="form-row">                        
                        <div class="form-group col-md-5">
                            <label>Cơ sở sản xuất</label>
                            <input type="text" wire:model.defer="co_so_san_xuat" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Nước sản xuất</label>
                            <input type="text" wire:model.defer="nuoc_san_xuat" class="form-control">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Nhà phân phối</label>
                            <input type="text" wire:model.defer="nha_phan_phoi" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">                        
                        <div class="form-group col-md-4">
                            <label>Hạn dùng GMP</label>
                            <input type="text" wire:model.defer="han_dung_gmp" class="form-control">
                        </div>                      
                        <div class="form-group col-md-8">
                            <label>Link Hồ sơ sản phẩm</label>
                            <input type="text" wire:model.defer="link_hssp" class="form-control">
                        </div>                      
                    </div>

                    
                  
                    
                    
                   
                </div>
            </div>
        </div>
    </div>

    {{-- BUTTONS --}}
    <div class="mt-3 text-right">
        <button  type="button" wire:click="cancel" class="btn btn-secondary">
            <i class="fas fa-save mr-1"></i> Quay lại
        </button>
        <button wire:click="save" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Lưu lại
        </button>
    </div>
  
</div>



    