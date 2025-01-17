<?php

namespace App\Repositories;

use App\Models\Survey;
use App\Interfaces\SurveyRepositoryInterface;

class SurveyRepository implements SurveyRepositoryInterface
{
    // Retrieve all surveys
    public function index()
    {
        return Survey::all();
    }

    // Retrieve surveys by ticket_id
    public function getById($id)
    {
        return Survey::where('ticket_id', $id)->get();
    }

    // Store a new survey
    public function store(array $data)
    {
        return Survey::create($data);
    }

    // Update an existing survey
    public function update(array $data, $id)
    {
        $survey = Survey::find($id);
        if ($survey) {
            $survey->update($data);
            return $survey;
        }
        return null;
    }

    // Delete a survey by its ID
    public function delete($id)
    {
        $survey = Survey::find($id);
        if ($survey) {
            $survey->delete();
            return true;
        }
        return false;
    }
}
