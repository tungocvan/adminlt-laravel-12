<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Helpers\TnvUserHelper;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\View;

 
class UserList extends Component
{
    use WithPagination, WithFileUploads;

    // Table & filters
    public $perPage = 5;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';

    // Selection
    public $selectedUsers = [];
    public $selectAll = false;

    // Modal states
    public $isEdit = false;

    // User fields
    public $userId = null; 
    public $name;
    public $username;
    public $email;
    public $password;
    public $birthdate;
    public $google_id;
    public $referral_code;
    public $is_admin = 0;
    public ?string $message = null;

    // Role
    public $role = null; // for create/edit
    public $selectedRoleId = null; // for role modal

    protected $listeners = [
        'refreshUsers' => '$refresh',
    ];

    protected $queryString = ['search', 'sortField', 'sortDirection', 'perPage'];

    protected $rulesCreate = [
        'name' => 'nullable|string',
        'email' => 'required|string|email|max:255|unique:users,email',
        'username' => 'nullable|string',
        'password' => 'required|string|min:6',
    ];

    protected $rulesUpdate = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'nullable|string|min:8',
    ];

    // -------- Computed properties --------
    public function getUsersProperty()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query
            ->paginate($this->perPage)
            ->appends(['search' => $this->search])
            ->withPath(route('user.index'));
    }

    #[On('refreshUsers')]
    public function refreshUsers($message = null)
    {
        // Nếu có message thì gán để hiển thị
        if ($message) {
            $this->message = $message;
        }
    }

    public function getRolesProperty()
    {
        return Role::orderBy('name')->pluck('name', 'id')->toArray();
    }

    // -------- Table & selection --------
    public function toggleSelectAll()
    {
        
        $this->selectedUsers = $this->selectAll ? $this->users->pluck('id')->toArray() : [];
    }
    
    
    public function updatedSelectedUsers(){
        $this->message !== null && $this->message = null;
    }

    public function updateUserRole()
    {
        // Không validate cứng, vì role/referral có thể chọn 1 trong 2
        $users = User::whereIn('id', $this->selectedUsers)->get();

        if ($users->isEmpty()) {
            session()->flash('error', 'Không có user nào được chọn!');
            return;
        }

        $role = null;

        // ✅ Nếu có selectedRoleId → xử lý role
        if (!empty($this->selectedRoleId)) {
            $role = Role::find($this->selectedRoleId);

            if (!$role) {
                session()->flash('error', 'Role không tồn tại!');
                return;
            }
        }

        foreach ($users as $user) {

            // ✅ Cập nhật role nếu có selectedRoleId
            if ($role) {
                $user->syncRoles([$role->name]);
            }

            // ✅ Cập nhật referral_code nếu có nhập
            if (!empty($this->referral_code)) {
                $user->referral_code = $this->referral_code;
                $user->save();
            }
        }

        // Reset
        $this->closeModalRole();
        $this->selectedUsers = [];
        $this->selectedRoleId = null;
        $this->referral_code = null;

        session()->flash('message', 'Cập nhật thành công!');
        $this->dispatch('modalRole'); // đóng modal
    }

   

    public function updatedPerPage()
    {
        $this->resetPage();
        // Cập nhật giá trị perPage
        // $this->perPage = (int) $value;

        // // Reset page về 1 mà KHÔNG làm thay đổi route thành /livewire/update
        // $this->resetPage();

        // // Giữ nguyên query string đúng (user?page=1&perPage=10)
        // $this->dispatch('refreshUsers');
    }

    // -------- Modal control --------
    public function openModal()
    {
        $this->resetForm();
        $this->dispatch('show-modal-user');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal-user');
        $this->resetForm();
    }

    public function openModalRole()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng.');
            return;
        }

        if (count($this->selectedUsers) === 1) {
            $u = User::with('roles')->find($this->selectedUsers[0]);
            $this->selectedRoleId = $u?->roles->pluck('id')->first() ?? null;
        } else {
            $this->selectedRoleId = null;
        }

        $this->dispatch('show-modal-role');
    }

    public function closeModalRole()
    {
        $this->dispatch('close-modal-role');
        $this->selectedRoleId = null;
    }

    protected function resetForm()
    {
        $this->reset(['userId', 'name', 'username', 'email', 'password', 'birthdate', 'google_id','referral_code', 'is_admin', 'isEdit', 'role']);
    }

    // -------- CRUD operations --------
    public function createUser()
    {
        
        $validated = $this->validate($this->rulesCreate);
        
        $data = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'name' => $this->name ?? null,
            'username' => $this->username ?? null,
            'is_admin' => $this->is_admin ?? 0,
            'birthdate' => $this->birthdate ?? null,
            'referral_code' => $this->referral_code ?? null,
            'google_id' => $this->google_id ?? null,
            'role_name' => $this->role ?? 'User',
        ];

        
        $result = TnvUserHelper::register($data);

        if ($result['status'] === 'success') {
            //$this->closeModal();
            //session()->flash('message', '✅ Tạo người dùng thành công!');
            $this->dispatch('refreshUsers',message:'✅ Tạo người dùng thành công!');
        } else {
            session()->flash('error', '❌ ' . $result['message']);
        }
    }

    public function editUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Không tìm thấy người dùng!');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->birthdate = $user->birthdate;
        $this->google_id = $user->google_id;
        $this->referral_code = $user->referral_code;
        $this->isEdit = true;
        $this->role = $user->roles->pluck('id')->first() ?? null;

        $this->dispatch('show-modal-user');
    }

    public function updateUser()
    {
        //dd($this->rulesUpdate);
        $this->validate(
            array_merge($this->rulesUpdate, [
                'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            ]),
        );

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'birthdate' => $this->birthdate,
            'google_id' => $this->google_id,
            'referral_code' => $this->referral_code,
        ];

        if (!empty($this->password)) {
            $data['password'] = $this->password;
        }

        $result = TnvUserHelper::updateUser($this->userId, $data);

        if ($result['status'] !== 'success') {
            session()->flash('error', $result['message'] ?? 'Cập nhật thất bại.');
            return;
        }

        if (!empty($this->role)) {
            $user = User::find($this->userId);

            if ($user) {
                $roleName = Role::find($this->role)?->name;
                if ($roleName) {
                    $user->syncRoles([$roleName]);
                }
            }
        }

        $this->dispatch('refreshUsers', message: '✅ Cập nhật người dùng thành công!');
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Không tìm thấy người dùng.');
            return;
        }

        if ($user->is_admin == -1) {
            session()->flash('error', 'Không thể xóa tài khoản admin.');
            return;
        }

        $user->delete();        
        $this->dispatch('refreshUsers',message: '🗑️ Xóa người dùng thành công!');
    }

    public function deleteSelectedUsers()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Chưa chọn người dùng nào!');
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)->get();
        foreach ($users as $user) {
            if ($user->is_admin != -1) {
                $user->delete();
            }
        }

        $this->selectedUsers = [];
        //session()->flash('message', '🗑️ Đã xóa người dùng được chọn!');
        $this->dispatch('refreshUsers', message:'🗑️ Đã xóa người dùng được chọn!');
    }

    // -------- Role assignment --------
    public function assignRoleToUsers()
    {
        $this->validate([
            'selectedRoleId' => 'required|exists:roles,id',
        ]);

        $role = Role::find($this->selectedRoleId);
        if (!$role) {
            session()->flash('error', 'Vai trò không tồn tại!');
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)->get();
        foreach ($users as $user) {
            $user->syncRoles([$role->name]);
        }

        $this->closeModalRole();
        $this->selectedUsers = [];
        session()->flash('message', '✅ Cập nhật vai trò thành công!');
        $this->dispatch('refreshUsers');
    }

    // -------- Approve / Verify --------
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);            
            $this->dispatch('refreshUsers',message:'✅ Người dùng đã được duyệt!');
        }else{
            $user->update(['email_verified_at' => null]);            
            $this->dispatch('refreshUsers',message:'✅ Đã duyệt bỏ xác thực!');
        }
    }


    // -------- Export --------
    public function exportSelected()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng.');
            return;
        }

        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.xlsx";

        return Excel::download(new UsersExport($this->selectedUsers), $fileName);
    }

    public function exportToPDF()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng.');
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)->get();
        $pdf = Pdf::loadView('exports.users-pdf', compact('users'));
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.pdf";

        return response()->streamDownload(fn() => print $pdf->output(), $fileName);
    }

     public function printUsers()
    {
        $users = User::whereIn('id', $this->selectedUsers)->get();
  
        if(count($users) == 0) {
            $this->error =  'Vui lòng chọn ít nhất một người dùng để in.';            
        }else{
             // Tạo nội dung HTML từ template
            $this->error ='';
            $html = View::make('exports.print-users', compact('users'))->render();
            $encodedHtml = base64_encode(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $this->dispatch('open-print-window', ['url' => 'data:text/html;base64,' . $encodedHtml]);        
        }
        } 

    // -------- Sorting --------
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->setPage(1);
        $this->message !== null && $this->message = null;
    }

    public function render()
    {
        return view('livewire.users.user-list', [
            'users' => $this->users,
            'roles' => $this->roles,
        ]);
    }
}
