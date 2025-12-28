<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProposalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'proposal.create',
            'proposal.view.own',
            'proposal.view.all',
            'proposal.approve',
            'proposal.reject',
        ];


        foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
        }


        $employee = Role::firstOrCreate(['name' => 'employee']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $director = Role::firstOrCreate(['name' => 'director']);


        $employee->givePermissionTo(['proposal.create', 'proposal.view.own']);
        $manager->givePermissionTo(['proposal.view.all', 'proposal.approve', 'proposal.reject']);
        $director->givePermissionTo(['proposal.view.all', 'proposal.approve', 'proposal.reject']);
    }
}
