<?php

namespace App\Imports;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Foundation\Auth\RegistersUsers;

class UsersImport implements ToModel, WithHeadingRow
{
    use RegistersUsers;

    public function model(array $row)
    {

        // Kiểm tra xem hàng có đủ dữ liệu không
        if (count($row) < 2) {
            return null; // Bỏ qua nếu không đủ dữ liệu
        }

        // Kiểm tra sự tồn tại của email
        if (User::where('email', $row['email'])->exists()) {
            return null; // Nếu email đã tồn tại, trả về null để bỏ qua
        }

        $nameRole = $row['role'] ?? 'User';
        $role = null;
        $roleId = Role::where('name',$nameRole)->get()[0]->id ?? null;


        if(!$roleId){
             $role = Role::create(['name' => $nameRole]);
             $roleId = $role->id;
        }

        if($role !== null){
            $permissions = Permission::where('name','admin-list')->orWhere('name','user-list')->pluck('id','id')->all();
            $role->syncPermissions($permissions);
        }

        $user = User::create([
            'name' => $row['name'] ?? 'Unknown', // Đặt giá trị mặc định nếu không có name
            'email' => $row['email'],
            'password' => empty($row['password']) ? Hash::make('12345678') : Hash::make($row['password']),
        ]);

        $user->assignRole([$roleId]);
        return $user;
    }
}
