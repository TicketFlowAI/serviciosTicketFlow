<?php

namespace App\Interfaces;

interface ServiceContractRepositoryInterface
{
    public function index();
    public function getById($id);
    public function store(array $data);
    public function update(array $data,$id);
    public function delete($id);
    public function getContractsByCompany($id);
    public function getDeleted();
    public function restore($id);
}
