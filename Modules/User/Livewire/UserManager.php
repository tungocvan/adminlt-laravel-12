<?php

namespace Modules\User\Livewire;


use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class UserManager extends Component
{
    use WithPagination;
    public $search = '';
     #[Url]
    public $perPage = 5;
     // Selection
    public $selected = [];
    public $selectAll = false;

    public $sortField = 'id';
    public $sortDirection = 'asc';
    
    // Modal states
    public $isEdit = false;
     // Role
    public $role = null; // for create/edit
    public $selectedRoleId = null; // for role modal

    public $user = [
        'userId' => null,
        'name' => null,
        'username' => null,
        'email' => null,
        'password' => null,
        'birthdate' => null,
        'google_id' => null,
        'referral_code' => null,
        'is_admin' => 0
    ];

    public function render()
    {
        return view('User::livewire.user-manager',[
            'users' => $this->users,
            'roles' => $this->roles,
        ]);
    }

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
            ->appends(['search' => $this->search]);
     
    }

    public function getRolesProperty()
    {
        return Role::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function toggleSelectAll()
    {       
        $this->selected = $this->selectAll ? $this->users->pluck('id')->toArray() : [];
    }
    public function updatedSelected()
    {
        // xử lý riêng biến Selected
    }
    public function updated()
    {
        // xử lý chung các biến 
    }
    public function edit($id)
    {
        // xử lý edit $id 

        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Không tìm thấy người dùng!');
            return;
        }
        dd($user);
    }
    public function delete($id)
    {
        // xử lý delete $id
        dd($id);
    }
    public function save()
    {
        // xử lý delete $id
        dd($this->user);
    }
    public function updateUser(){
        dd($this->user);
    }

    public function approveUser($id)
    {
        // xử lý delete $id
        dd($id);
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // $this->setPage(1);
        // $this->message !== null && ($this->message = null);
    }
    public function openModal()
    {
        //dd('openModal new/edit');        
        // bắn sự kiện về để mở modal
       // $this->resetForm();
        $this->dispatch('show-modal-user');
    }
    public function deleteSelected()
    {
        dd($this->selected);
        // bắn sự kiện về để mở modal
    }
    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset($this->user);
    }
} 