<?php

namespace App\Interfaces;

interface SurveyRepositoryInterface
{
    public function getById($id);
    public function store(array $data);
}
