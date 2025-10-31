<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $formVisible = false;
    public $isEditMode = false;
    public $name;
    public $roleId;

    public $permissionsByModule = [];
    public $selectedPermissions = [];
    public $selectAll = false;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $permissions = Permission::all();

        $this->permissionsByModule = $permissions->groupBy(function ($perm) {
            return explode('-', $perm->name)[0] ?? 'Khác';
        })->map(fn($group) => $group->toArray())->toArray();
    }

    public function updatedSelectAll($value)
    {
        $this->selectedPermissions = $value
            ? Permission::pluck('name')->toArray()
            : [];
    }

    public function create()
    {
        $this->reset(['name', 'roleId', 'selectedPermissions']);
        $this->isEditMode = false;
        $this->formVisible = true;
        $this->resetPage();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditMode = true;
        $this->formVisible = true;
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        $role = $this->isEditMode
            ? Role::findOrFail($this->roleId)
            : Role::create(['name' => $this->name]);

        $role->syncPermissions($this->selectedPermissions);

        $this->formVisible = false;
        $this->dispatch('notify', [
            'message' => $this->isEditMode ? 'Cập nhật thành công!' : 'Tạo mới thành công!'
        ]);
    }

    public function cancel()
    {
        $this->formVisible = false;
    }

    public function togglePermission($permissionName)
    {
        if (in_array($permissionName, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionName]);
        } else {
            $this->selectedPermissions[] = $permissionName;
        }
    }

    public function toggleModule($module)
    {
        $modulePermissions = Permission::where('name', 'like', "{$module}-%")->pluck('name')->toArray();
        $hasAll = collect($modulePermissions)->every(fn($p) => in_array($p, $this->selectedPermissions));

        if ($hasAll) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, $modulePermissions);
        } else {
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $modulePermissions));
        }
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.role.role-list', compact('roles'));
    }
}
