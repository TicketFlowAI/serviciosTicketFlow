<?php

namespace App\Repositories;
use App\Interfaces\TaxRepositoryInterface;
use App\Models\Tax;

class TaxRepository implements TaxRepositoryInterface
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
        return Tax::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return Tax::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return Tax::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return Tax::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        Tax::destroy($id);
    }
}
