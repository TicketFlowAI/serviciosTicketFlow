<?php

namespace App\Repositories;
use App\Interfaces\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository
{
    /**
     * Retrieves all items.
     */
    public function index(){
        return User::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id){
       return User::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data){
       return User::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data,$id){
       return User::whereId($id)->update($data);
    }
    
    /**
     * Deletes an Item by ID.
     */
    public function delete($id){
       User::destroy($id);
    }
}