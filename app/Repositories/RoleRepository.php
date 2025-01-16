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

    public function store(array $data)
    {
        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);
        return $role;
    }

    public function update(array $data, $id)
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);
        return $role;
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->exists()) {
            throw new \Exception('Cannot delete this role, there are users assigned to it');
        }

        $role->delete();
    }
}
