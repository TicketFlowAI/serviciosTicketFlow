<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'client',
            'technician',
            'super-admin',
            '1',
            '2',
            '3'
        ];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }

        // Create permissions
        $viewUsersPermission = Permission::create(['name' => 'view-users']);
        Permission::create(['name' => 'modify-roles']);
        // Assign permission to the super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $superAdminRole->givePermissionTo($viewUsersPermission);
    }
}
