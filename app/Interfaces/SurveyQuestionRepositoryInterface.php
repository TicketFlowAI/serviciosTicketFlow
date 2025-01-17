<?php

namespace App\Interfaces;

use App\Models\SurveyQuestion;

interface SurveyQuestionRepositoryInterface
{
    public function index();
    public function store(array $details): SurveyQuestion;
    public function getById($id): SurveyQuestion;
    public function update(array $details, $id): bool;
    public function delete($id): bool;
    public function restore($id): bool;
    public function getDeleted();
}
