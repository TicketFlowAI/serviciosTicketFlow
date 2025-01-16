<?php

namespace App\Repositories;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Models\ServiceContract;

class ServiceContractRepository implements ServiceContractRepositoryInterface
{
   /**
    * Create a new class instance.
    */
   public function __construct()
   {
      //s
   }

   /**
    * Retrieves all items.
    */
   public function index()
   {
      return ServiceContract::all();
   }

   /**
    * Retrieves an item by ID.
    */
   public function getById($id)
   {
      return ServiceContract::findOrFail($id);
   }

   /**
    * Creates a new item in the repository.
    */
   public function store(array $data)
   {
      return ServiceContract::create($data);
   }

   /**
    * Updates an Item by ID.
    */
   public function update(array $data, $id)
   {
      return ServiceContract::whereId($id)->update($data);
   }

   /**
    * Deletes an Item by ID.
    */
   public function delete($id)
   {
      ServiceContract::destroy($id);
   }

   /**
    * Retrieves all deleted items.
    */
   public function getDeleted()
   {
      return ServiceContract::onlyTrashed()->get();
   }

   /**
    * Restores a deleted item by ID.
    */
   public function restore($id)
   {
      return ServiceContract::withTrashed()->where('id', $id)->restore();
   }

   public function getContractsByCompany($id)
   {
      return ServiceContract::where('company_id',$id)->get();
   }
}
