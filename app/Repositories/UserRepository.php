<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\User;

class UserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieves all items.
     */
    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->companyObject = Company::where('id', $user->company_id)->first();
            $user->role = $user->getRoleNames();
        }
        return $users;
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return User::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        User::destroy($id);
    }
}