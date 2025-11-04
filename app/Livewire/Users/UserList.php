<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\View;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Helpers\TnvUserHelper;

class UserList extends Component
{
    use WithPagination, WithFileUploads;

    // Table / filter
    public $perPage = 5;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';

    // Selection
    public $selectedUsers = [];
    public $selectAll = false;

    // Modal / form state
    public $isEdit = false;

    // User fields
    public $userId = null;
    public $name;
    public $username;
    public $email;
    public $password;
    public $birthdate;
    public $google_id;
    public $is_admin = 0;

    // Role
    public $role = null;              // For create/edit
    public $selectedRoleId = null;    // For role modal

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

    protected $updatesQueryString = ['search','sortField','sortDirection','perPage'];

    // ---------- Computed properties ----------
    public function getUsersProperty()
    {
        $query = User::query();

        if($this->search){
            $query->where(function($q){
                $q->where('name','like','%'.$this->search.'%')
                  ->orWhere('email','like','%'.$this->search.'%')
                  ->orWhere('username','like','%'.$this->search.'%');
            });
        }

        $query->orderBy($this->sortField,$this->sortDirection);
        return $query->paginate($this->perPage)
             ->withQueryString()
             ->withPath(route('user.index')); // route gốc

    }

    public function getRolesProperty()
    {
        return Role::orderBy('name')->pluck('name','id')->toArray();
    }

    // ---------- Selection ----------
    public function toggleSelectAll()
    {
        if($this->selectAll){
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        session()->put('selectedUsers',$this->selectedUsers);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // ---------- Modal Open / Close ----------
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
        if(empty($this->selectedUsers)){
            session()->flash('error','Vui lòng chọn ít nhất một người dùng.');
            return;
        }

        if(count($this->selectedUsers)===1){
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

    // ---------- Form Handling ----------
    protected function resetForm()
    {
        $this->reset(['name','username','email','password','birthdate','google_id','userId','isEdit','role','is_admin']);
    }

    public function save()
    {
        $validated = $this->validate($this->rulesCreate);

        $data = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'name' => $validated['name'],
            'username' => $validated['username'],
            'is_admin' => $this->is_admin ?? 0,
            'birthdate' => $this->birthdate ?? null,
            'google_id' => $this->google_id ?? null,
            'role_name' => $this->role ?? 'User',
        ];

        $result = TnvUserHelper::register($data);

        if($result['status'] === 'success'){
            $this->closeModal();
            session()->flash('message','✅ User created successfully!');
            $this->dispatch('refreshUsers');
        } else {
            session()->flash('error','❌ '.$result['message']);
        }
    }

    public function edit($userId)
    {
        $user = User::find($userId);
        if(!$user){
            session()->flash('error','User not found!');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->birthdate = $user->birthdate;
        $this->google_id = $user->google_id;
        $this->isEdit = true;
        $this->role = $user->roles->pluck('id')->first() ?? null;

        $this->dispatch('show-modal-user');
    }

    public function update()
    {
        $this->validate(array_merge($this->rulesUpdate, [
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->userId
        ]));

        $data = [
            'name'=>$this->name,
            'email'=>$this->email,
            'username'=>$this->username,
            'birthdate'=>$this->birthdate,
            'google_id'=>$this->google_id,
        ];

        if(!empty($this->password)){
            $data['password'] = $this->password;
        }

        $result = TnvUserHelper::updateUser($this->userId,$data);

        if($result['status'] !== 'success'){
            $msg = $result['message'] ?? 'Update failed';
            session()->flash('error',$msg);
            return;
        }

        if(!empty($this->role)){
            $user = User::find($this->userId);
            if($user){
                $user->syncRoles([$this->role]);
            }
        }

        $this->closeModal();
        session()->flash('message','User updated successfully!');
        $this->dispatch('refreshUsers');
    }

    // ---------- Delete ----------
    public function delete($userId)
    {
        
        $user = User::find($userId);
       
        if(!$user){
            session()->flash('error','User not found!');
            return;
        }

        if($user->is_admin == -1){
            session()->flash('error','Cannot delete admin user');
            return;
        }

        $user->delete();
        session()->flash('message','User deleted successfully!');
        $this->dispatch('refreshUsers');
    }

    public function deleteSelected()
    {
        if(empty($this->selectedUsers)){
            session()->flash('error','No users selected!');
            return;
        }
      

        $users = User::whereIn('id',$this->selectedUsers)->get();
        foreach($users as $user){
            if($user->is_admin != -1){
                $user->delete();
            }
        }

        $this->selectedUsers = [];
        session()->flash('message','Selected users deleted!');
        $this->dispatch('refreshUsers');
    }

    // ---------- Role ----------
    public function updateUserRole()
    {
        $this->validate([
            'selectedRoleId' => 'required|exists:roles,id'
        ]);
    
        $role = Role::find($this->selectedRoleId); // Lấy role model từ ID
        if (!$role) {
            session()->flash('error', 'Role không tồn tại!');
            return;
        }
    
        $users = User::whereIn('id', $this->selectedUsers)->get();
        foreach ($users as $user) {
            $user->syncRoles([$role->name]); // Truyền tên role, không phải ID
        }
    
        $this->closeModalRole();
        $this->selectedUsers = [];
        session()->flash('message', 'Cập nhật vai trò thành công!');
        $this->dispatch('modalRole');
    }
    

    // ---------- Approve ----------
    public function approve($id)
    {
        $user = User::findOrFail($id);
        if(is_null($user->email_verified_at)){
            $user->update(['email_verified_at'=>now()]);
            session()->flash('message','Người dùng đã được duyệt!');
            $this->dispatch('refreshUsers');
        }
    }

    // ---------- Export ----------
    public function exportSelected()
    {
        if(empty($this->selectedUsers)){
            session()->flash('error','Vui lòng chọn ít nhất một người dùng.');
            return;
        }
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.xlsx";

        return Excel::download(new UsersExport($this->selectedUsers),$fileName);
    }

    public function exportToPDF()
    {
        if(empty($this->selectedUsers)){
            session()->flash('error','Vui lòng chọn ít nhất một người dùng.');
            return;
        }
        $users = User::whereIn('id',$this->selectedUsers)->get();
        $pdf = Pdf::loadView('exports.users-pdf',compact('users'));
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.pdf";

        return response()->streamDownload(fn()=>print($pdf->output()),$fileName);
    }

    // ---------- Sorting ----------
    public function sortBy($field)
    {
        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection==='asc'?'desc':'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.users.user-list',[
            'users'=>$this->users,
            'roles'=>$this->roles,
        ]);
    }
}
