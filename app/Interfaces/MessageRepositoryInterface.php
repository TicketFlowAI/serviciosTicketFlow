<?php

namespace App\Interfaces;

interface MessageRepositoryInterface
{
    public function getById($id);
    public function store(array $data);
}
