<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-12 col-md-10" style="display:inline-flex">
                <button wire:click="openModal" style="width: 100px;" class="btn btn-outline-success btn-sm"><i class="fa fa-plus"></i> Add</button> 
                <button wire:click="deleteSelected" onclick="return confirm('Are you sure you want to delete selected users?')" style="width: 100px;" class="btn btn-outline-danger btn-sm mx-2"><i class="fa fa-trash"></i> Delete All</button>             
                 <button wire:click="openModalRole" style="width: 150px;" class="btn btn-outline-success btn-sm mr-2"><i class="fa fa-save"></i> Update All Roles</button>
                <form wire:submit.prevent="importFile" x-data="{ uploading: false }">
                    <div>
                        <div class="btn btn-outline-success btn-sm btn-file">
                            <!-- Hiển thị trạng thái chờ -->
                            <i class="fas fa-paperclip"></i> Import Excel
                            <input type="file" wire:model="file" @change="uploading = true" wire:disabled="isImporting">
                            <span class="help-block">Max 100 rows</span> 
                            @error('file') <span class="error">{{ $message }}</span> @enderror                                        
                        </div>
                
                        <!-- Nút Upload hiển thị khi uploading = true -->
                        <button x-show="uploading" type="submit" style="width: 100px;" class="btn btn-outline-success btn-sm">
                            Upload File
                        </button>
                
                        <!-- Hiển thị trạng thái tải lên của Livewire -->
                        <div wire:loading wire:target="file">Uploading...</div>   
                    </div>
                </form>
                
                
            </div>            
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-md-10 d-flex">               
                <div class="form-group mr-2" style="width:150px">                    
                    <select wire:model.change="perPage"  class="form-control custom-select">                      
                      <option value="5">Show 05 rows</option>
                      <option value="10">Show 10 rows</option>
                      <option value="50">Show 50 rows</option>
                      <option value="100">Show 100 rows</option>                      
                    </select>
                </div>
                <div>
                    <button x-on:click="$wire.printUsers()" class="btn buttons-print btn-default"  title="Print"><span><i class="fas fa-fw fa-lg fa-print"></i></span></button>         
                    <button wire:click="exportSelected" class="btn buttons-excel buttons-html5 btn-default"  title="Export to Excel"><span><i class="fas fa-fw fa-lg fa-file-excel text-success"></i></span></button> 
                    <button wire:click="exportToPDF" class="btn buttons-pdf buttons-html5 btn-default"  title="Export to PDF"><span><i class="fas fa-fw fa-lg fa-file-pdf text-danger"></i></span></button> 
                </div>               
            </div>
            <div class="col-sm-12 col-md-2">
                <div class="input-group input-group-sm float-right">
                    <input type="text" wire:model.live.debounce.250ms="search" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
            </div>
        </div>
        
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <div x-data>  
      <table class="table table-hover text-nowrap">
        <thead>
          <tr>
            <th>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input form-check-label" wire:model="selectAll" wire:click="toggleSelectAll">
                    <label class="form-check-label" for="exampleCheck1"></label>
                </div>
            </th>            
            <th>ID</th>
            <th><a href="#" wire:click.prevent="sortBy('name')">Name</a></th>
            <th><a href="#" wire:click.prevent="sortBy('email')">Email</a></th>
            <th>Roles</th>
            <th>Verified</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            @foreach($this->users as $user)
                <tr>
                    <td> 
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model="selectedUsers" value="{{ $user->id }}">                
                        </div>
                    </td>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if(!empty($user->getRoleNames()))
                          @foreach($user->getRoleNames() as $v)
                             <label class="badge bg-success">{{ $v }}</label>
                          @endforeach
                        @endif
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Đã duyệt</span>
                        @else
                        <button wire:click="approve({{ $user->id }})" class="btn btn-outline-success btn-sm">
                            <i class="fa fa-check"></i> Duyệt
                        </button>
                        
                        @endif
                    </td>
                    
                    <td>
                        <div class="btn-group flex-wrap d-flex" style="width:150px">
                            <button wire:click="edit({{ $user->id }})" class="btn btn-outline-primary btn-sm mr-1"><i class="fa fa-edit"></i> Edit</button>
                            <button wire:click="delete({{ $user->id }})" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    </td>
                </tr>
            @endforeach    
       
        </tbody>
      </table>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="row">            
            <div class="col-sm-12 col-md-12">
                {{ $this->users->links(data: ['scrollTo' => false]) }}
            </div>
    </div>
     <!-- Modal Add - Edit -->
     <div class="modal fade @if($showModal) show @endif" style="display: @if($showModal) block @else none @endif;" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit User' : 'Add User' }}</h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                        <div class="form-group">
                            <input type="text" wire:model="name" class="form-control" placeholder="Name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" wire:model="email" class="form-control" placeholder="Email">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <select class="form-control" wire:model="role">
                                @foreach ($this->roles as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                 @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" {{ $isEdit ? 'disabled' : '' }} wire:model="username" class="form-control" placeholder="User Name">
                            @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <input type="password" wire:model="password" class="form-control" placeholder="Password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Role -->
     <div class="modal fade @if($showModalRole) show @endif" style="display: @if($showModalRole) block @else none @endif;" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">UPDATE ROLES</h5>
                    <button type="button" class="close" wire:click="closeModalRole" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateRole">                        
                        <div class="form-group">
                            <select class="form-control" wire:model="role">
                                @foreach ($this->roles as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                 @endforeach
                            </select>
                        </div>                 
                        <button type="submit" class="btn btn-primary">Update All</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="notification">
                     
    </div>
</div>
<script>
    window.addEventListener('open-print-window', event => {
        let newWindow = window.open('', '_blank');
        if (newWindow) {
            let decodedHtml = atob(event.detail[0].url.split(',')[1]); // Giải mã base64
            newWindow.document.open();
            newWindow.document.write(decodedHtml);
            newWindow.document.close();
            newWindow.print();
            setTimeout(() => newWindow.close(), 1000); // Đóng sau khi in
        } else {
            alert('Trình duyệt chặn popup! Hãy kiểm tra cài đặt.');
        }
    });

</script>

{{-- @if(auth()->user()->is_admin)
    <script type="module">
        console.log('aaa');
            window.Echo.channel('users')
                .listen('.create', (data) => {
                    console.log('Order status updated: ', data);
                    var d1 = document.getElementById('notification');
                    d1.insertAdjacentHTML('beforeend', '<div class="alert alert-success alert-dismissible fade show"><span><i class="fa fa-circle-check"></i>  '+data.message+'</span></div>');
                });
    </script>
@endif --}}

{{-- <script type="module">   

    if(window.Echo) {
        console.log('echo js');
    window.Echo.channel('users')
        .listen('.create', (data) => {
            console.log('Order status updated: ', data);
            var d1 = document.getElementById('notification');
            d1.insertAdjacentHTML('beforeend', '<div class="alert alert-success alert-dismissible fade show"><span><i class="fa fa-circle-check"></i>  '+data.message+'</span></div>');
        });
    }
</script> --}}

<script type="module">   

    if(window.Echo) {
        console.log('echo Registered users js');
        window.Echo.channel('users')
        .listen('.UserRegistered', (e) => {
            console.log('Người dùng mới đăng ký:', e);
        });
    }
</script>

</div>
