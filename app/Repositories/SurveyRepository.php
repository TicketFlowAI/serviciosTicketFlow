<?php

namespace App\Repositories;

use App\Models\Survey;
use App\Interfaces\SurveyRepositoryInterface;

class SurveyRepository implements SurveyRepositoryInterface
{
    // Retrieve surveys by ticket_id and load specific fields in relationships
    public function getById($id)
    {
        return Survey::with([
            'user:id,name,lastname',
            'questions:id,question'
        ])->where('ticket_id', $id)->get();

    }

    // Store a new survey
    public function store(array $data)
    {
        return Survey::create($data);
    }
}
