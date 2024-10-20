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
         $serviceContract->CompanyObject = Company::where('id', $serviceContract->company_id)->first();
         $serviceContract->serviceObject = Service::where('id', $serviceContract->service_id)->first();
         $serviceContract->serviceTermObject = ServiceTerm::where('id', $serviceContract->service_term_id)->first();
      }
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
}