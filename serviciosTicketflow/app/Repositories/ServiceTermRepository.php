<?php

namespace App\Repositories;
use App\Interfaces\ServiceTermRepositoryInterface;
use App\Models\ServiceTerm;

class ServiceTermRepository implements ServiceTermRepositoryInterface
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
       return ServiceTerm::all();
    }
 
    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
       return ServiceTerm::findOrFail($id);
    }
 
    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
       return ServiceTerm::create($data);
    }
 
    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
       return ServiceTerm::whereId($id)->update($data);
    }
 
    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
       ServiceTerm::destroy($id);
    }
}
