<?php

namespace App\Repositories;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceContract;
use App\Models\ServiceTerm;

class ServiceContractRepository implements ServiceContractRepositoryInterface
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
      $serviceContracts = ServiceContract::all();
      foreach ($serviceContracts as $serviceContract) {
         $serviceContract->companyObject = Company::where('id', $serviceContract->company_id)->first();
         $serviceContract->serviceObject = Service::where('id', $serviceContract->service_id)->first();
         $serviceContract->serviceTermObject = ServiceTerm::where('id', $serviceContract->service_term_id)->first();
         $serviceContract->price = $serviceContract->serviceObject->price / $serviceContract->serviceTermObject->months;
      }
      return $serviceContracts;
   }

   /**
    * Retrieves an item by ID.
    */
   public function getById($id)
   {
      $object = ServiceContract::findOrFail($id);
      $object->companyObject = Company::where('id', $object->company_id)->first();
      $object->serviceObject = Service::where('id', $object->service_id)->first();
      $object->serviceTermObject = ServiceTerm::where('id', $object->service_term_id)->first();
      $object->price = $object->serviceObject->price / $object->serviceTermObject->months;
      return $object;  
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
}
