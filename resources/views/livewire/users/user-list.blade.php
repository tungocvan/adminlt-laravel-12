<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Quản lý người dùng</h5>
            <div>
                <button wire:click="openModal" class="btn btn-light btn-sm">
                    <i class="fa fa-plus mr-1"></i> Thêm mới
                </button>
                <button wire:click="openModalRole" class="btn btn-light btn-sm">
                    <i class="fa fa-user-shield mr-1"></i> Cập nhật Role
                </button>
                <button wire:click="deleteSelected"
                        onclick="return confirm('Bạn có chắc muốn xóa các user đã chọn?')"
                        class="btn btn-danger btn-sm">
                    <i class="fa fa-trash mr-1"></i> Xóa chọn
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Search & Filter --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Tìm kiếm...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.change="perPage" class="form-control form-control-sm">
                        <option value="5">Hiển thị 5</option>
                        <option value="10">Hiển thị 10</option>
                        <option value="50">Hiển thị 50</option>
                    </select>
                </div>
                <div class="col-md-5 text-right">
                    <button wire:click="printUsers" class="btn btn-outline-secondary btn-sm" title="In danh sách"><i class="fas fa-print"></i></button>
                    <button wire:click="exportSelected" class="btn btn-outline-success btn-sm" title="Xuất Excel"><i class="fas fa-file-excel"></i></button>
                    <button wire:click="exportToPDF" class="btn btn-outline-danger btn-sm" title="Xuất PDF"><i class="fas fa-file-pdf"></i></button>
                </div>
            </div>

            {{-- Alert --}}
            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="40"><input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"></th>
                            <th wire:click="sortBy('id')" style="cursor:pointer;">ID <i class="fas fa-sort text-muted"></i></th>
                            <th wire:click="sortBy('name')" style="cursor:pointer;">Tên <i class="fas fa-sort text-muted"></i></th>
                            <th wire:click="sortBy('email')" style="cursor:pointer;">Email <i class="fas fa-sort text-muted"></i></th>
                            <th>Role</th>
                            <th>Xác thực</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->users as $user)
                            <tr>
                                <td><input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}"></td>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge badge-info">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">Đã duyệt</span>
                                    @else
                                        <button wire:click="approve({{ $user->id }})" class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-check"></i> Duyệt
                                        </button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button wire:click="edit({{ $user->id }})" class="btn btn-outline-primary btn-sm"><i class="fa fa-edit"></i></button>
                                        <button wire:click="delete({{ $user->id }})" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có người dùng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $this->users->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>

    {{-- Include Form Modal --}}
    @include('livewire.users.user-form')

    {{-- Include Role Modal (Alpine-based) --}}
    @include('livewire.users.user-form-role')

</div>
