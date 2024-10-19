<?php

namespace App\Repositories;
use App\Interfaces\ServiceRepositoryInterface;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceTerm;
use App\Models\Tax;

class ServiceRepository implements ServiceRepositoryInterface
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
      $services = Service::all();
      foreach ($services as $service) {
         $service->taxObject = Tax::where('id', $service->tax_id)->first();
         $service->categoryObject = Category::where('id', $service->category_id)->first();
     }
      return $services;
   }

   /**
    * Retrieves an item by ID.
    */
   public function getById($id)
   {
      return Service::findOrFail($id);
   }

   /**
    * Creates a new item in the repository.
    */
   public function store(array $data)
   {
      return Service::create($data);
   }

   /**
    * Updates an Item by ID.
    */
   public function update(array $data, $id)
   {
      return Service::whereId($id)->update($data);
   }

   /**
    * Deletes an Item by ID.
    */
   public function delete($id)
   {
      Service::destroy($id);
   }
}
