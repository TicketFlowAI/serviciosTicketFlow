<?php

namespace App\Repositories;

use App\Interfaces\SurveyQuestionRepositoryInterface;
use App\Models\SurveyQuestion;

class SurveyQuestionRepository implements SurveyQuestionRepositoryInterface
{
    public function index()
    {
        return SurveyQuestion::all();
    }

    public function store(array $details): SurveyQuestion
    {
        return SurveyQuestion::create($details);
    }

    public function getById($id): SurveyQuestion
    {
        return SurveyQuestion::findOrFail($id);
    }

    public function update(array $details, $id): bool
    {
        $surveyQuestion = SurveyQuestion::findOrFail($id);
        return $surveyQuestion->update($details);
    }

    public function delete($id): bool
    {
        $surveyQuestion = SurveyQuestion::findOrFail($id);
        return $surveyQuestion->delete();
    }

    public function restore($id): bool
    {
        $surveyQuestion = SurveyQuestion::withTrashed()->findOrFail($id);
        return $surveyQuestion->restore();
    }

    public function getDeleted()
    {
        return SurveyQuestion::onlyTrashed()->get();
    }
}
