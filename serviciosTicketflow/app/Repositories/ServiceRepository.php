<?php

namespace App\Repositories;
use App\Interfaces\ServiceRepositoryInterface;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function index(){
        return Service::all();
    }

    public function getById($id){
       return Service::findOrFail($id);
    }

    public function store(array $data){
       return Service::create($data);
    }

    public function update(array $data,$id){
       return Service::whereId($id)->update($data);
    }
    
    public function delete($id){
       Service::destroy($id);
    }
}
