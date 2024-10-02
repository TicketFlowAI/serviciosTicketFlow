<?php

namespace App\Repositories;
use App\Interfaces\CompanyRepositoryInterface;
use App\Models\Company;

class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * Retrieves all items.
     */
    public function index(){
        return Company::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id){
       return Company::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data){
       return Company::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data,$id){
       return Company::whereId($id)->update($data);
    }
    
    /**
     * Deletes an Item by ID.
     */
    public function delete($id){
       Company::destroy($id);
    }
}
