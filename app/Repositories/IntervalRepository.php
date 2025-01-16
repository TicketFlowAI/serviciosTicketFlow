<?php

namespace App\Repositories;

use App\Interfaces\IntervalRepositoryInterface;
use App\Models\Interval;

class IntervalRepository implements IntervalRepositoryInterface
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
        return Interval::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return Interval::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return Interval::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return Interval::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        Interval::destroy($id);
    }

    /**
     * Retrieves all deleted items.
     */
    public function getDeleted()
    {
        return Interval::onlyTrashed()->get();
    }

    /**
     * Restores a deleted item by ID.
     */
    public function restore($id)
    {
        return Interval::withTrashed()->where('id', $id)->restore();
    }
}
