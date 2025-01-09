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
        Role::create(['name' => 'client']);
        Role::create(['name' => 'technician']);
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => '1']);
        Role::create(['name' => '2']);
        Role::create(['name' => '3']);


        Permission::create(['name' => 'view-users']);

        
    }
}
