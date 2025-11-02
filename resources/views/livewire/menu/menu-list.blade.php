<div>
    {{-- Alert thông báo --}}
    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif
    @if ($permissionError)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {!! $permissionError !!}
        </div>
    @endif

    {{-- Section Icon Guide --}}
    <section class="content mb-3">
        <div class="container-fluid">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-icons mr-2"></i>Icons Menu</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        Xem danh sách biểu tượng tại:
                        <a href="https://fontawesome.com/v5/icons?t=categories/" target="_blank">
                            Font Awesome 5 Icons
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Nút thao tác --}}
    <div class="mb-3">
        <div class="btn-group flex-wrap">
            <button wire:click="addItem" class="btn btn-outline-success btn-sm">
                <i class="fa fa-plus"></i> Thêm Menu
            </button>
            <button wire:click="addItemSubmenu" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-plus"></i> Thêm Submenu
            </button>
            <button wire:click="showMenu('backup')" class="btn btn-outline-info btn-sm">
                <i class="fa fa-download"></i> Backup Menu
            </button>
            <button wire:click="showMenu('restore')" class="btn btn-outline-warning btn-sm">
                <i class="fa fa-upload"></i> Restore Menu
            </button>
        </div>
    </div>

    {{-- Danh sách menu --}}
    <ul class="list-group shadow-sm">
        @foreach ($menuItems as $index => $item)
            @if (isset($item['header']))
                {{-- Header Item --}}
                <li class="list-group-item bg-light font-weight-bold d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge badge-success mr-2">{{ $index }}</span>
                        {{ $item['header'] }}
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button wire:click="editItem('{{ json_encode($item) }}')" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="deleteItem('{{ $index }}')" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button wire:click="duplicateItem('{{ json_encode($item) }}', {{ $index }})"
                            class="btn btn-primary">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button wire:click="moveUp({{ $index }})" class="btn btn-secondary"
                            @if ($index == 0) disabled @endif>
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button wire:click="moveDown({{ $index }})" class="btn btn-secondary"
                            @if ($index == count($menuItems) - 1) disabled @endif>
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>
                </li>
            @else
                {{-- Menu Item --}}
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge badge-primary mr-2">{{ $index }}</span>
                            <i class="{{ $item['icon'] }}"></i> {{ $item['text'] }}
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button wire:click="editItem('{{ json_encode($item) }}')" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteItem('{{ $index }}')" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button wire:click="duplicateItem('{{ json_encode($item) }}', {{ $index }})"
                                class="btn btn-primary">
                                <i class="fas fa-copy"></i>
                            </button>
                            @if ($item['url'] == '#')
                                <button wire:click="addSubMenuByIndex('{{ $index }}')" class="btn btn-info">
                                    <i class="fas fa-level-down-alt"></i>
                                </button>
                            @endif
                            <button wire:click="moveUp({{ $index }})" class="btn btn-secondary"
                                @if ($index == 0) disabled @endif>
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button wire:click="moveDown({{ $index }})" class="btn btn-secondary"
                                @if ($index == count($menuItems) - 1) disabled @endif>
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submenu --}}
                    @if (isset($item['submenu']))
                        <ul class="list-group ml-4 mt-2">
                            @foreach ($item['submenu'] as $key => $submenu)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-light mr-2">{{ $key + 1 }}</span>
                                        <i class="{{ $submenu['icon'] }}"></i> {{ $submenu['text'] }}
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="editItem('{{ json_encode($submenu) }}')"
                                            class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deleteItem('{{ $index }}','{{ $key }}')"
                                            class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button
                                            wire:click="duplicateItem('{{ json_encode($submenu) }}', {{ $index }}, {{ $key }})"
                                            class="btn btn-primary">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button wire:click="moveUp('{{ $index }}','{{ $key }}')"
                                            class="btn btn-secondary" @if ($key == 0) disabled @endif>
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button wire:click="moveDown('{{ $index }}','{{ $key }}')"
                                            class="btn btn-secondary"
                                            @if ($key == count($menuItems[$index]['submenu']) - 1) disabled @endif>
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>

    {{-- Modal chỉnh sửa Menu --}}
    @include('livewire.menu.partials.modal-edit')

    {{-- Modal Backup / Restore --}}
    @include('livewire.menu.partials.modal-backup')

    {{-- Modal loading khi đang ghi file --}}
    <div class="modal fade @if ($isSaving) show d-block @endif" tabindex="-1" role="dialog"
        style="@if (!$isSaving) display:none; @endif">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content p-4 text-center">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                </div>
                <h5>Đang lưu dữ liệu...</h5>
                <small class="text-muted">Vui lòng chờ trong giây lát</small>
            </div>
        </div>
    </div>
    @if ($isSaving)
        <div class="modal-backdrop fade show"></div>
    @endif

</div>
