<?php

namespace App\Interfaces;

interface TicketRepositoryInterface
{
    public function index();
    public function getById($id);
    public function store(array $data);
    public function update(array $data,$id);
    public function getTicketsByCompany($id);
    public function getTicketsByTechnician($id);
    public function getDeleted();
    public function restore($id);
    public function delete($id); // Add this line
}
