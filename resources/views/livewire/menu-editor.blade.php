<div>
    <h1>Edit Menu</h1>

    @if(session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="list-group">
        @foreach($menu as $index => $item)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="row w-100">
                    <div class="col-4">
                        @if(isset($item['header']))
                        <input  type="text" wire:model="menu.{{ $index }}.header" placeholder="Header" class="form-control mb-2" />
                        @else
                            <div class="mb-2 flex-grow-1">
                                <input  type="text" wire:model="menu.{{ $index }}.text" placeholder="Text" class="form-control" />
                                <input  type="text" wire:model="menu.{{ $index }}.url" placeholder="URL" class="form-control" />
                                <input  type="text" wire:model="menu.{{ $index }}.icon" placeholder="Icon" class="form-control" />
                                <input  type="text" wire:model="menu.{{ $index }}.can" placeholder="Permission" class="form-control" />
                            </div>
                        @endif
                    </div>
                    <div class="col-8">
                        <div class="item-menu d-flex">
                            <button wire:click="moveUp({{ $index }})" class="btn btn-secondary btn-sm mx-1" @if($index == 0) disabled @endif>↑</button>
                            <button wire:click="moveDown({{ $index }})" class="btn btn-secondary btn-sm mx-1" @if($index == count($menu) - 1) disabled @endif>↓</button>
                            <button wire:click="removeItem({{ $index }})" class="btn btn-danger btn-sm mx-1">Delete</button>
                        </div>
        
                        
        
                        @if(isset($item['submenu']))
                    
                       
                            <ul class="list-group mt-2">
                                
                                @foreach($item['submenu'] as $subIndex => $subItem)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <input style="width:500px" type="text" wire:model="menu.{{ $index }}.submenu.{{ $subIndex }}.text" placeholder="Sub Item Text" class="form-control mb-2" />
                                        <div>
                                            <button wire:click="moveSubItemUp({{ $index }}, {{ $subIndex }})" class="btn btn-secondary btn-sm" @if($subIndex == 0) disabled @endif>↑</button>
                                            <button wire:click="moveSubItemDown({{ $index }}, {{ $subIndex }})" class="btn btn-secondary btn-sm" @if($subIndex == count($item['submenu']) - 1) disabled @endif>↓</button>
                                            <button wire:click="removeSubItem({{ $index }}, {{ $subIndex }})" class="btn btn-danger btn-sm">Delete</button>
                                            <button wire:click="duplicateSubItem({{ $index }}, {{ $subIndex }})" class="btn btn-warning btn-sm mt-2">Duplicate</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            
                        @else
                           {{-- <button wire:click="duplicateSubItem({{ $index }}, {{ $subIndex }})" class="btn btn-warning btn-sm mt-1 mx-1">Duplicate</button> --}}
                        @endif
                        
                    </div>
                </div>
               

                
            </li>
        @endforeach
    </ul>

    <button wire:click="$set('showModal', true)" class="btn btn-secondary mt-3">Add Item</button>
    <button wire:click="save" class="btn btn-primary mt-3">Save Menu</button>

    <!-- Modal -->
    <div class="modal fade @if($showModal) show @endif" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="{{ $showModal ? 'false' : 'true' }}" style="@if($showModal) display: block; @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" wire:model="newItem.text" placeholder="Text" class="form-control mb-2" />
                    <input type="text" wire:model="newItem.url" placeholder="URL" class="form-control mb-2" />
                    <input type="text" wire:model="newItem.icon" placeholder="Icon" class="form-control mb-2" />
                    <input type="text" wire:model="newItem.can" placeholder="Permission" class="form-control mb-2" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Close</button>
                    <button wire:click="addItem" type="button" class="btn btn-primary" wire:click="$set('showModal', false)">Add Item</button>
                </div>
            </div>
        </div>
    </div>
</div>

