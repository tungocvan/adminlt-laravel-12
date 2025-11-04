<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Events\UserCreate;

class UserList extends Component
{
    use WithPagination, WithFileUploads;

    // Table / filter
    public $perPage = 5;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';

    // File import
    public $file;
    public $isImporting = false;

    // Selection
    public $selectedUsers = [];
    public $selectAll = false;

    // Modal / form state
    public $showModal = false;        // create / edit user
    public $showModalRole = false;    // modal update role (single or bulk)
    public $isEdit = false;

    // User fields
    public $name;
    public $username;
    public $email;
    public $password;
    public $birthdate;
    public $google_id;

    // Role handling
    public $role = null;              // for create/edit (role name or id)
    public $selectedRoleId = null;    // for modal role (role id)
    public $selectedUserId = null;    // when updating single user by modal

    // ui errors
    public $error;

    protected $rulesCreate = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'username' => 'required|string|max:255|unique:users,username',
        'password' => 'required|string|min:8',
    ];

    protected $rulesUpdate = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'nullable|string|min:8',
    ];


    protected $listeners = [
        'refreshUsers' => '$refresh',
        'closeModalRole' => 'closeModalRole', // nhận emit từ JS khi modal đóng
        'modalRoleClosed' => 'resetRoleModal'
    ];
    
    public function resetRoleModal()
    {
        $this->showModalRole = false;
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
    }

    // ---------- Computed properties ----------
    public function getUsersProperty()
    {
        return User::where(function ($q) {
                if ($this->search) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate((int) $this->perPage);
    }

    public function getRolesProperty()
    {
        // trả về array [id => name] để dùng trong <select>
        return Role::orderBy('name')->pluck('name', 'id')->toArray();
    }

    // ---------- Selection helpers ----------
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        // lưu tạm (nếu cần)
        session()->put('selectedUsers', $this->selectedUsers);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // ---------- Import ----------
    public function importFile()
    {
        $this->validate([
            'file' => 'required|file|max:32768|mimes:xlsx,xls,csv',
        ]);

        $this->isImporting = true;
        try {
            Excel::import(new UsersImport, $this->file);
            $this->reset('file');
            session()->flash('message', 'Users imported successfully!');
            $this->dispatch('refreshUsers');
        } catch (\Exception $e) {
            session()->flash('error', 'Import error: ' . $e->getMessage());
        } finally {
            $this->isImporting = false;
        }
    }

    // ---------- CRUD ----------
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    protected function resetForm()
    {
        $this->reset(['name', 'username', 'email', 'password', 'birthdate', 'google_id', 'userId', 'isEdit', 'role', 'userId']);
        $this->isEdit = false;
        $this->userId = null;
    }

    public function save()
    {
        $validated = $this->validate($this->rulesCreate);

        $validated['password'] = Hash::make($validated['password']);

        try {
            $user = User::create($validated);

            // Nếu role được chọn theo id => lấy role name để assign hoặc dùng syncRoles với id.
            if ($this->role) {
                // supports id or name; dùng syncRoles với id an toàn
                $user->syncRoles([$this->role]);
            }

            event(new UserCreate($user));
            $this->resetForm();
            $this->showModal = false;
            session()->flash('message', 'User created successfully!');
            $this->dispatch('refreshUsers');
        } catch (\Exception $e) {
            session()->flash('error', 'Save error: ' . $e->getMessage());
        }
    }

    public function edit($userId)
    {
        $user = User::find($userId);
        if (! $user) {
            session()->flash('error', 'User not found!');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->birthdate = $user->birthdate;
        $this->google_id = $user->google_id;
        $this->isEdit = true;
        // set role to first role id if exists
        $firstRoleId = $user->roles->pluck('id')->first();
        $this->role = $firstRoleId ?: null;

        $this->showModal = true;
    }

    public function update()
    {
        $this->validate(array_merge($this->rulesUpdate, [
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
        ]));

        try {
            $user = User::findOrFail($this->userId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            // optional fields
            $data['username'] = $this->username;
            $data['birthdate'] = $this->birthdate;
            $data['google_id'] = $this->google_id;

            $user->update($data);

            if ($this->role) {
                // syncRoles nhận id hoặc tên; dùng id để nhất quán
                $user->syncRoles([$this->role]);
            }

            $this->resetForm();
            $this->showModal = false;
            session()->flash('message', 'User updated successfully!');
            $this->dispatch('refreshUsers');
        } catch (\Exception $e) {
            session()->flash('error', 'Update error: ' . $e->getMessage());
        }
    }

    public function delete($userId)
    {
        $user = User::find($userId);
        if (! $user) {
            session()->flash('error', 'User not found!');
            return;
        }

        if ($user->is_admin == -1) {
            session()->flash('error', 'Cannot delete admin user');
            return;
        }

        try {
            $user->delete();
            session()->flash('message', 'User deleted successfully!');
            $this->dispatch('refreshUsers');
        } catch (\Exception $e) {
            session()->flash('error', 'Delete error: ' . $e->getMessage());
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'No users selected for deletion!');
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)->get();
        $delError = [];

        foreach ($users as $user) {
            if ($user->is_admin == -1) {
                $delError[] = $user->email;
                continue;
            }
            try {
                $user->delete();
            } catch (\Exception $e) {
                $delError[] = $user->email;
            }
        }

        if (! empty($delError)) {
            session()->flash('error', 'Không thể xóa: ' . implode(', ', $delError));
        } else {
            $this->selectedUsers = [];
            session()->flash('message', 'Selected users deleted successfully!');
        }

        $this->dispatch('refreshUsers');
    }

    // ---------- Role update (modal) ----------
    /**
     * Mở modal cập nhật role.
     * Nếu $userId được truyền => cập nhật cho 1 user.
     * Nếu không truyền => modal dùng cho bulk update (dựa vào selectedUsers).
     */
    public function openModalRole($userId = null)
    {
        $this->selectedUserId = $userId;
        $this->selectedRoleId = null;
    
        if ($userId) {
            $user = User::with('roles')->find($userId);
            $this->selectedRoleId = $user->roles->pluck('id')->first() ?: null;
        } else {
            if (count($this->selectedUsers) === 1) {
                $u = User::with('roles')->find($this->selectedUsers[0]);
                $this->selectedRoleId = $u->roles->pluck('id')->first() ?: null;
            }
        }
    
        $this->showModalRole = true;
    
        // dispatch browser event để JS mở modal (không render 'show' từ server)
        $this->dispatch('user-form-role:open');
    }

    public function closeModalRole()
    {
        $this->showModalRole = false;
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        $this->dispatch('user-form-role:close');
    }

    public function updateRole()
{
    $this->validate([
        'selectedRoleId' => 'required|exists:roles,id',
    ]);

    $roleId = $this->selectedRoleId;

    try {
        if ($this->selectedUserId) {
            $user = User::findOrFail($this->selectedUserId);
            $user->syncRoles([$roleId]);
        } else {
            if (empty($this->selectedUsers)) {
                session()->flash('error', 'Vui lòng chọn ít nhất một người dùng để cập nhật role.');
                return;
            }
            $users = User::whereIn('id', $this->selectedUsers)->get();
            foreach ($users as $u) {
                $u->syncRoles([$roleId]);
            }
        }

        $this->showModalRole = false;
        $this->selectedUserId = null;
        $this->selectedRoleId = null;
        session()->flash('message', 'User Role updated successfully!');
        $this->dispatch('user-form-role:close');
        $this->dispatch('refreshUsers');
        $this->resetRoleModal();
        $this->loadData();
    } catch (\Exception $e) {
        session()->flash('error', 'Update role error: ' . $e->getMessage());
    }
}

    // ---------- Export / Print ----------
    public function exportSelected()
    {
        if (empty($this->selectedUsers)) {
            $this->error = 'Vui lòng chọn ít nhất một người dùng để xuất Excel.';
            return;
        }
        $this->error = '';
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.xlsx";

        return Excel::download(new UsersExport($this->selectedUsers), $fileName);
    }

    public function exportToPDF()
    {
        if (empty($this->selectedUsers)) {
            $this->error = 'Vui lòng chọn ít nhất một người dùng để xuất PDF.';
            return;
        }
        $this->error = '';
        $users = User::whereIn('id', $this->selectedUsers)->get();
        $pdf = Pdf::loadView('exports.users-pdf', compact('users'));
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $fileName
        );
    }

    public function printUsers()
    {
        $users = User::whereIn('id', $this->selectedUsers)->get();

        if ($users->count() == 0) {
            $this->error = 'Vui lòng chọn ít nhất một người dùng để in.';
            return;
        }

        $this->error = '';
        $html = View::make('exports.print-users', compact('users'))->render();
        $encodedHtml = base64_encode(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $this->dispatch('open-print-window', ['url' => 'data:text/html;base64,' . $encodedHtml]);
    }

    // ---------- Utilities ----------
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);
            session()->flash('message', 'Người dùng đã được duyệt thành công!');
            $this->dispatch('refreshUsers');
        }
    }

    public function render()
    {
        return view('livewire.users.user-list', [
            'users' => $this->users,          // collection paginated
            'roles' => $this->roles,          // roles array id => name
        ]);
    }
}
