<?php



use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $userId;
    public $isEdit = false;
    public $selectedUsers = [];
    public $showModal = false;
    public $file;

    public $perPage = 10; // Số lượng bản ghi mặc định hiển thị
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $selectAll = false;

    public function mount()
    {
        // No initialization needed
    }

    public function save()
    {
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        $this->reset(['name', 'email', 'password']);
        $this->showModal = false;
        session()->flash('message', 'User created successfully!');
    }

    public function uploadUser()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);
        Excel::import(new UsersImport, $this->file);
        $this->reset('file');
        session()->flash('message', 'Users imported successfully!');
    }

    public function edit($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->isEdit = true;
            $this->showModal = true;
        } else {
            session()->flash('error', 'User not found!');
        }
    }

    public function update()
    {
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        User::find($this->userId)->update($validated);
        $this->reset(['name', 'email', 'password', 'userId', 'isEdit']);
        $this->showModal = false;
        session()->flash('message', 'User updated successfully!');
    }

    public function delete($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            session()->flash('message', 'User deleted successfully!');
        } else {
            session()->flash('error', 'User not found!');
        }
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedUsers)) {
            User::whereIn('id', $this->selectedUsers)->delete();
            $this->selectedUsers = [];
            session()->flash('message', 'Selected users deleted successfully!');
        } else {
            session()->flash('error', 'No users selected for deletion!');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
     
        $this->resetPage(); // Đặt lại trang khi thay đổi số lượng bản ghi
    }

    public function clearSearch()
    {
        $this->search = ''; // Đặt giá trị tìm kiếm về trống
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage(); // Đặt lại trang khi thay đổi sắp xếp
    }

    public function getUsersProperty()    {
        
        return User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate((int) $this->perPage);
    }

   
    public function openModal()
    {
        $this->reset(['name', 'email', 'password', 'userId', 'isEdit']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
};

?>

<div class="container mt-5">
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <button wire:click="openModal" class="btn btn-primary mb-3">Add New</button>
    <button wire:click="deleteSelected" onclick="return confirm('Are you sure you want to delete selected users?')" class="btn btn-danger mb-3">Delete Selected</button>

    <form wire:submit.prevent="uploadUser" class="mb-3">
        <input type="file" wire:model="file" class="form-control mb-2">
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror
        <button type="submit" class="btn btn-success">Upload Users</button>
    </form>

    <select wire:model.change="perPage" class="form-control mb-3">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
    </select>
    <div class="mb-3">
        <input type="text" wire:model.live.debounce.150ms="search" class="form-control" placeholder="Search users...">
    </div>
    <p>Hiển thị {{ $perPage }} bản ghi mỗi trang</p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>
                    <a href="#" wire:click.prevent="sortBy('name')">Name</a>
                </th>
                <th>
                    <a href="#" wire:click.prevent="sortBy('email')">Email</a>
                </th>
                <th>Actions</th>
                <th>
                    <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" />
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($this->users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button wire:click="edit({{ $user->id }})" class="btn btn-warning btn-sm">Edit</button>
                        <button wire:click="delete({{ $user->id }})" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                    <td>
                        <input type="checkbox" wire:model="selectedUsers" value="{{ $user->id }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <nav aria-label="Page navigation">
        <ul class="pagination">
            {{ $this->users->links(data: ['scrollTo' => false]) }}
        </ul>
    </nav>

    <!-- Modal -->
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
                            <input type="password" wire:model="password" class="form-control" placeholder="Password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Save' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>