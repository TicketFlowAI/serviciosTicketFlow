<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
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
        return User::all();
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

    /**
     * Brings the current user.
     */
    public function getAuthenticatedUser($request)
    {
        return $request->user();
    }

    /**
     * Retrieves all deleted items.
     */
    public function getDeleted()
    {
        return User::onlyTrashed()->get();
    }

    /**
     * Restores a deleted item by ID.
     */
    public function restore($id)
    {
        return User::withTrashed()->where('id', $id)->restore();
    }
}
