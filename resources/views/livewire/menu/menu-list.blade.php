<div>
    @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
    @endif
    <section class="content">
        <div class="container-fluid">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Icons Menu</h3>
            </div> <!-- /.card-body -->
            <div class="card-body">            
              <div>
                <a href="https://fontawesome.com/v5/icons?t=categories/" target="_blank">Font Awesome</a><br>
                <br>
                <br>
              </div>
            </div><!-- /.card-body -->
          </div>
        </div><!-- /.container-fluid -->
      </section>
    <div class="row">        
        <div class="col-md-12">
            <div class="form-group">
                <button wire:click="addItem" style="width: 150px;" class="btn btn-outline-success btn-sm my-2"><i class="fa fa-plus"></i> Thêm mới menu</button> 
                <button wire:click="addItemSubmenu" style="width: 200px;" class="btn btn-outline-primary btn-sm my-2"><i class="fa fa-plus"></i> Thêm mới Submenu</button> 
                <button wire:click="showMenu('backup')" style="width: 200px;" class="btn btn-outline-primary btn-sm my-2"><i class="fa fa-plus"></i> Backup Menu</button> 
                <button wire:click="showMenu('restore')" style="width: 200px;" class="btn btn-outline-primary btn-sm my-2"><i class="fa fa-plus"></i> Restore Menu</button> 
                <ul class="list-group">
                    @foreach($menuItems as $index => $item)
                        @if(isset($item['header']))
                            <li class="list-group-item header">
                                <button type="button" class="btn btn-success">
                                    <span class="badge badge-light">{{ $index }}</span>
                                </button>
                                {{ $item['header'] }}
                                <button wire:click="editItem('{{ json_encode($item) }}')" class="btn btn-warning btn-sm">Edit</button>
                                <button wire:click="deleteItem('{{ $index }}')" class="btn btn-danger btn-sm">Delete</button>
                                <button wire:click="duplicateItem('{{ json_encode($item) }}', {{ $index }})" class="btn btn-primary btn-sm">Duplicate</button>
                                <button wire:click="moveUp({{ $index }})" class="btn btn-secondary btn-sm" @if($index == 0) disabled @endif>Up</button>
                                <button wire:click="moveDown({{ $index }})" class="btn btn-secondary btn-sm" @if($index == count($menuItems) - 1) disabled @endif>Down</button>
                              
                            </li>
                        @else
                            <li class="list-group-item ">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="submenu" >
                                            <button type="button" class="btn btn-primary">
                                                <span class="badge badge-light">{{ $index }}</span>
                                            </button>
                                            <a href="#">
                                                <i class="{{ $item['icon'] }}"></i> {{ $item['text'] }}
                                            </a>
                                            @if(isset($item['submenu']))
                                                <ul class="list-group mt-2">
                                                    @foreach($item['submenu'] as $key => $submenu)
                                                        <li key="{{$key}}" class="list-group-item submenu-item">
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <button type="button" class="btn btn-warning">
                                                                        <span class="badge badge-light">{{ $index - $key }}</span>
                                                                    </button>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <a href="#">
                                                                        <i class="{{ $submenu['icon'] }}"></i> {{ $submenu['text'] }}
                                                                    </a>
                                                                </div>
                                                                <div class="col-md-7 float-end">                                                        
                                                                    <button wire:click="editItem('{{ json_encode($submenu) }}')" class="btn btn-warning btn-sm">Edit</button>
                                                                    <button wire:click="deleteItem('{{ $index }}','{{ $key }}')" class="btn btn-danger btn-sm">Delete</button>
                                                                    <button wire:click="duplicateItem('{{ json_encode($submenu) }}', {{ $index }},{{ $key }})" class="btn btn-primary btn-sm">Duplicate</button>
                                                                    {{-- @if($submenu['url'] == "#" )
                                                                        <button wire:click="addSubMenuByIndex('{{ $index }}','{{ $key }}')" class="btn btn-primary btn-sm">Add SubMenu</button>
                                                                    @endif                                          --}}
                                                                    <button wire:click="moveUp('{{ $index }}','{{ $key }}')" class="btn btn-secondary btn-sm" @if($key == 0) disabled @endif>Up</button>
                                                                    <button wire:click="moveDown('{{ $index }}','{{ $key }}')" class="btn btn-secondary btn-sm" @if($key == count($menuItems[$index]['submenu']) - 1) disabled @endif>Down</button>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                            
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="px-2" >                                    
                                            <button wire:click="editItem('{{ json_encode($item) }}')" class="btn btn-warning btn-sm">Edit</button>
                                            <button wire:click="deleteItem('{{ $index }}')" class="btn btn-danger btn-sm">Delete</button>
                                            <button wire:click="duplicateItem('{{ json_encode($item) }}', {{ $index }})" class="btn btn-primary btn-sm">Duplicate</button>
                                            @if($item['url'] == "#" )
                                            <button wire:click="addSubMenuByIndex('{{ $index }}')" class="btn btn-primary btn-sm">Add SubMenu</button>
                                            @endif
                                            <button wire:click="moveUp({{ $index }})" class="btn btn-secondary btn-sm" @if($index == 0) disabled @endif>Up</button>
                                            <button wire:click="moveDown({{ $index }})" class="btn btn-secondary btn-sm" @if($index == count($menuItems) - 1) disabled @endif>Down</button>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
    <!-- Modal -->
    <div class="modal fade @if($showModal) show @endif"  style="@if($showModal) display: block; @endif">    
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@if(!$addMenu) Chỉnh sửa Menu @else Thêm mới Menu @endif</h5>
                    <button type="button" class="btn-close" wire:click="closeModal">x</button>
                </div>
                <div class="modal-body">   

                    <form>
                        @if(isset($menuHeader))
                            <div class="mb-3">
                                <label for="menuHeader" class="form-label">Tên Menu</label>
                                <input type="text" class="form-control"  wire:model="menuHeader">
                            </div>      
                            <div class="mb-3">
                                <label for="menuCan" class="form-label">Can</label>
                                <input type="text" class="form-control"  wire:model="menuCan">
                            </div> 
                        @else
                            <div class="mb-3">
                                <label for="menuText" class="form-label">Tên Menu</label>
                                <input type="text" class="form-control"  wire:model="menuText">
                            </div>    
                            <div class="mb-3">
                                <label for="menuUrl" class="form-label">URL</label>
                                <input type="text" class="form-control"  wire:model="menuUrl">
                            </div>
                            <div class="mb-3">
                                <label for="menuIcon" class="form-label">Icon</label>
                                <input type="text" class="form-control" wire:model="menuIcon">
                            </div>
                            <div class="mb-3">
                                <label for="menuCan" class="form-label">Can</label>
                                <input type="text" class="form-control"  wire:model="menuCan">
                            </div>
                        @endif
                    </form>
                 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Đóng</button>
                    <button type="button" class="btn btn-primary" wire:click="updateMenu">Cập nhật</button>
                </div>
            </div>
        </div>
   </div>
    <!-- Modal -->
    <div class="modal fade @if($showMenuModal) show @endif"  style="@if($showMenuModal) display: block; @endif" style="width:500px">    
        <div class="modal-dialog">
            <div class="modal-content" style="width:700px!important">
                <div class="modal-header">
                    <h5 class="modal-title">Backup - Restore Menu</h5>
                    <button type="button" class="btn-close"  wire:click="showCloseMenu">x</button>
                </div>
                <div class="modal-body">   

                    <form>
                        @if($actionMenu == 'backup')
                            <div class="mb-3">
                                <label for="nameJson" class="form-label">Name</label>
                                <input type="text" class="form-control"  wire:model="nameJson">
                            </div>       
                        @else
                            <div class="mb-3">
                                <ul class="list-group">
                                    @foreach($backupFiles as $file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $file }}
                                            <div>
                                                <button wire:click.prevent="restoreFile('{{ $file }}')" class="btn btn-success btn-sm">Restore</button>
                                                <button wire:click.prevent="deleteFile('{{ $file }}')" class="btn btn-danger btn-sm">Delete</button>
                                                <button wire:click.prevent="downloadFile('{{ $file }}')" class="btn btn-danger btn-sm">Download</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>                      
                        @endif
                    </form>
                    
                 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="showCloseMenu">Đóng</button>
                    @if($actionMenu == 'backup')
                     <button type="button" class="btn btn-primary" wire:click="updateMenuJson">Cập nhật</button>
                    @endif
                </div>
            </div>
        </div>
   </div>
</div>
