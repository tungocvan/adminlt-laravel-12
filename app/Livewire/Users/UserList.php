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
use App\Helpers\TnvUserHelper;

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
    public $showModal = false;        // create / edit user (Alpine entangle)
    public $showModalRole = false;    // role modal (Alpine entangle)
    public $isEdit = false;

    // User fields (create/edit)
    public $userId = null;
    public $name;
    public $username;
    public $email;
    public $password;
    public $birthdate;
    public $google_id;
    public $is_admin = 0;

    // Role handling
    public $role = null;              // role id for create/edit
    public $selectedRoleId = null;    // role id for role modal

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
    ];
    protected $updatesQueryString = ['search', 'sortField', 'sortDirection', 'perPage'];
    // ---------- Computed properties ----------
    public function getUsersProperty()
    {
        $query = User::query();

        // 🔹 Tìm kiếm keyword nếu có
        if ($this->search) {
            $query->keyword($this->search); // gọi scopeKeyword trong model
        }

        // 🔹 Sắp xếp
        $query->orderBy($this->sortField, $this->sortDirection);

        // 🔹 Phân trang
        return $query->paginate((int) $this->perPage);
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
        $this->reset(['name', 'username', 'email', 'password', 'birthdate', 'google_id', 'userId', 'isEdit', 'role', 'is_admin']);
        $this->isEdit = false;
        $this->userId = null;
    }

    public function save()
    {
        $validated = $this->validate($this->rulesCreate);

        // Chuẩn bị dữ liệu gửi cho helper
        $data = [
            'email'      => $validated['email'],
            'password'   => $validated['password'],  // chưa hash, helper sẽ tự xử lý
            'name'       => $validated['name'] ?? null,
            'username'   => $validated['username'] ?? null,
            'is_admin'   => $this->is_admin ?? 0,
            'birthdate'  => $this->birthdate ?? null,
            'google_id'  => $this->google_id ?? null,
            'role_name'  => $this->role ?? 'User',
        ];

        try {
            $result = TnvUserHelper::register($data);

            if ($result['status'] === 'success') {
                $user = $result['data'];

                $this->resetForm();
                $this->showModal = false;
                session()->flash('message', '✅ User created successfully!');
                $this->dispatch('refreshUsers');
            } else {
                session()->flash('error', '❌ ' . $result['message']);
            }
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
            // --- Chuẩn bị dữ liệu cập nhật ---
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'username' => $this->username,
                'birthdate' => $this->birthdate,
                'google_id' => $this->google_id,
            ];

            // ✅ Nếu có nhập mật khẩu mới thì thêm vào (để helper tự xử lý hash)
            if (!empty($this->password)) {
                $data['password'] = $this->password;
            }

            // --- Gọi helper cập nhật ---
            $result = TnvUserHelper::updateUser($this->userId, $data);

            if ($result['status'] !== 'success') {
                $errorMsg = $result['message'] ?? 'Update failed';
                if (!empty($result['errors'])) {
                    foreach ($result['errors']->toArray() as $field => $messages) {
                        $errorMsg .= ' | ' . $field . ': ' . implode(', ', $messages);
                    }
                }
                session()->flash('error', $errorMsg);
                return;
            }

            // --- Cập nhật vai trò (nếu có chọn) ---
            if (!empty($this->role)) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->syncRoles([$this->role]);
                }
            }

            // --- Reset form và đóng modal ---
            $this->resetForm();
            $this->showModal = false;

            session()->flash('message', 'User updated successfully!');
            $this->dispatch('refreshUsers');

        } catch (\Throwable $e) {
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
    public function openModalRole()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng để cập nhật vai trò.');
            return;
        }

        // Nếu chỉ chọn 1 user → load role hiện tại
        if (count($this->selectedUsers) === 1) {
            $u = User::with('roles')->find($this->selectedUsers[0]);
            $this->selectedRoleId = $u?->roles->pluck('id')->first() ?? null;
        } else {
            $this->selectedRoleId = null;
        }

        $this->showModalRole = true; // Alpine @entangle sẽ mở modal
    }

    public function closeModalRole()
    {
        $this->showModalRole = false;
        $this->selectedRoleId = null;
    }

    public function updateUserRole()
    {
        $this->validate([
            'selectedRoleId' => 'required|exists:roles,id',
        ]);

        $users = User::whereIn('id', $this->selectedUsers)->get();
        foreach ($users as $user) {
            $user->syncRoles([$this->selectedRoleId]);
        }

        $this->showModalRole = false;
        $this->selectedUsers = [];
        $this->selectedRoleId = null;
        session()->flash('message', 'Cập nhật vai trò thành công!');
        $this->dispatch('refreshUsers');
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
            'users' => $this->users,
            'roles' => $this->roles,
        ]);
    }
}
