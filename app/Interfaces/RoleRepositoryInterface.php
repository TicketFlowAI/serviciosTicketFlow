<?php

namespace App\Interfaces;

interface RoleRepositoryInterface
{
    public function index();
    public function getById($id);
    public function update(array $data, $id);
}
