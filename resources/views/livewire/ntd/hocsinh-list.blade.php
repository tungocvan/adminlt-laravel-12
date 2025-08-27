<div class="py-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">Quản lý Học sinh</h3>
            <button class="btn btn-primary btn-sm" wire:click="create">+ Thêm học sinh</button>
        </div>

        <div class="card-body">
            <input type="text" class="form-control mb-3" placeholder="Tìm kiếm..." wire:model.live="search">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Lớp</th>
                        <th>Họ và tên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Mã định danh</th>
                        <th>GVCN</th>
                        <th>Báo mẫu</th>
                        <th width="120px">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hocsinhs as $key => $hs)
                        <tr>
                            <td>{{ $hocsinhs->firstItem() + $key }}</td>
                            <td>{{ $hs->lop }}</td>
                            <td>{{ $hs->ho_va_ten }}</td>
                            <td>{{ $hs->ngay_sinh }}</td>
                            <td>{{ $hs->gioi_tinh }}</td>
                            <td>{{ $hs->ma_dinh_danh_hoc_sinh }}</td>
                            <td>{{ $hs->gvcn }}</td>
                            <td>{{ $hs->bao_mau }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" wire:click="edit({{ $hs->id }})">Sửa</button>
                                <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $hs->id }})">Xóa</button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="py-2">
                {{ $hocsinhs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="hocsinhModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Sửa' : 'Thêm' }} học sinh</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng" wire:click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" class="form-control" wire:model="ho_va_ten">
                            @error('ho_va_ten') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Lớp</label>
                            <input type="text" class="form-control" wire:model="lop">
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" class="form-control" wire:model="ngay_sinh">
                        </div>
                        <div class="form-group">
                            <label>Giới tính</label>
                            <input type="text" class="form-control" wire:model="gioi_tinh">
                        </div>
                        <div class="form-group">
                            <label>Mã định danh</label>
                            <input type="text" class="form-control" wire:model="ma_dinh_danh_hoc_sinh">
                            @error('ma_dinh_danh_hoc_sinh') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>GVCN</label>
                            <input type="text" class="form-control" wire:model="gvcn">
                        </div>
                        <div class="form-group">
                            <label>Bảo mẫu</label>
                            <input type="text" class="form-control" wire:model="bao_mau">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            wire:click="closeModal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('showModal', () => {
            $('#hocsinhModal').modal('show');
        });

        Livewire.on('hideModal', () => {
            $('#hocsinhModal').modal('hide');
        });
        Livewire.on('showConfirm', () => {
            if (confirm("Bạn có chắc chắn muốn xóa?")) {
                Livewire.find(@this.__instance.id).call('delete');
            }
        });
        
    });
</script>

