<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function index()
    {
        return Role::with('permissions')->get(); // Eager load permissions
    }

    public function getById($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);
        return $role;
    }
}
