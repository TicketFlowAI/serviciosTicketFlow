<?php

namespace App\Repositories;

use App\Interfaces\EmailRepositoryInterface;
use App\Models\Email;

class EmailRepository implements EmailRepositoryInterface
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
        return Email::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return Email::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return Email::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return Email::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        Email::destroy($id);
    }

    /**
     * Retrieves all deleted items.
     */
    public function getDeleted()
    {
        return Email::onlyTrashed()->get();
    }

    /**
     * Restores a deleted item by ID.
     */
    public function restore($id)
    {
        return Email::withTrashed()->where('id', $id)->restore();
    }
}
