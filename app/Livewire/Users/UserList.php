<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User; 
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Events\UserCreate;
use App\Events\UserRegistered;

class UserList extends Component
{
    use WithPagination, WithFileUploads;
    public $perPage = 5; // Số lượng bản ghi mặc định hiển thị
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $file;
    public $isImporting = false;

    public $selectedUsers = [];
    public $selectAll = false;

    public $showModal = false;
    public $showModalRole = false;
    public $isEdit = false;
    public $name;
    public $username;
    public $email;
    public $password;
    public $userId;
    public $role=['admin'];

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function importFile()
    {
     
        $this->validate([
            'file' => 'required|file|max:32768|mimes:xlsx,xls,csv',
        ]);
        // dd($this->file);
        $this->isImporting = true; // Đặt trạng thái đang nhập

        try {
            Excel::import(new UsersImport, $this->file);
            $this->reset('file');
            session()->flash('message', 'Users imported successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isImporting = false; // Đặt lại trạng thái sau khi hoàn tất
        }
    }

    public function updatedPerPage()
    {
     
        $this->resetPage(); // Đặt lại trang khi thay đổi số lượng bản ghi
    }

    public function getUsersProperty()
    {
        return User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate((int) $this->perPage);
    }
    public function getRolesProperty()
    {
        
        return Role::pluck('name','name')->all();
    }
  
    public function approve($id)
    {
        
        $user = User::findOrFail($id);
        if (is_null($user->email_verified_at)) {
            
            $user->update([
                'email_verified_at' => now(),
            ]);
            session()->flash('success', 'Người dùng đã được duyệt thành công!');
        }
    }
    

    public function render()
    {
        
        return view('livewire.users.user-list');
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

    public function save()
    {
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);        
        
        $validated['password'] = Hash::make($validated['password']);             
        $user = User::create($validated);
        $roleId = Role::where('name',$this->role)->get()[0]->id ?? null;  
        //dd($this->role); 
        $user->assignRole([$roleId]);
        event(new UserCreate($user));
        $this->reset(['name', 'email', 'password']);
        $this->showModal = false;
        session()->flash('message', 'User created successfully!');

    }

    public function edit($userId)
    {
        $user = User::find($userId);
        //dd($user->getRoleNames());
        $roleId = Role::all();        
        if ($user) {
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->username = $user->username;
            $this->isEdit = true;
            $this->showModal = true;
            //dd($user->getRoleNames()[0]);
            $this->role = $user->getRoleNames()[0] ?? 'User';
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

        $user = User::find($this->userId);
        $roleId = Role::where('name',$this->role)->get()[0]->id ;  
        if(isset($user->getRoleNames()[0]))  $user->removeRole($user->getRoleNames()[0]);    
                    
        $user->update($validated);           
        $user->assignRole([$roleId]);
        
        $this->reset(['name', 'email', 'password', 'userId', 'isEdit','role']);
        $this->showModal = false;
        session()->flash('message', 'User updated successfully!');
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

    public function openModal()
    {
        $this->reset(['name', 'email', 'password', 'userId', 'isEdit', 'role']);
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updateRole()
    {

        $roleId = Role::where('name',$this->role)->get()[0]->id ?? null;  
        //$this->reset(['name', 'email', 'password', 'userId', 'isEdit']);
        //dd($this->selectedUsers);       
        foreach ($this->selectedUsers as $user) {            
            $userRole = User::find($user);
            if(isset($userRole->getRoleNames()[0])){
                $userRole->removeRole($userRole->getRoleNames()[0]);
            }
            
            $userRole->assignRole([$roleId]);
        }
        $this->showModalRole = false;
        session()->flash('message', 'User Role updated successfully!');
    }
    public function openModalRole()
    {
        //$this->reset(['name', 'email', 'password', 'userId', 'isEdit']);
        //dd($this->selectedUsers);
        $this->showModalRole = true;
    }
    public function closeModalRole()
    {
        $this->showModalRole = false;
    }


    public function exportSelected()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng để xuất Excel.');
            return;
        }

        // Lấy ngày giờ hiện tại
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        // Đặt tên file theo định dạng "users-list-yyyy-mm-dd-HH-MM.xlsx"
        $fileName = "users-list-{$timestamp}.xlsx";

        return Excel::download(new UsersExport($this->selectedUsers), $fileName);
    }
    public function exportToPDF()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Vui lòng chọn ít nhất một người dùng để xuất PDF.');
            return;
        }

        $users = User::whereIn('id', $this->selectedUsers)->get();

        // Render view PDF
        $pdf = Pdf::loadView('exports.users-pdf', compact('users'));

        // Tạo tên file theo thời gian
        $timestamp = Carbon::now()->format('Y-m-d-H-i');
        $fileName = "users-list-{$timestamp}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $fileName
        );
    }   

    public function updatedSelectedUsers()
    {
        session()->put('selectedUsers', $this->selectedUsers);
    }

    public function printUsers()
    {
        $users = User::whereIn('id', $this->selectedUsers)->get();

        // Tạo nội dung HTML từ template
        $html = View::make('exports.print-users', compact('users'))->render();
        $encodedHtml = base64_encode(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $this->dispatch('open-print-window', ['url' => 'data:text/html;base64,' . $encodedHtml]);        
    }

}
 