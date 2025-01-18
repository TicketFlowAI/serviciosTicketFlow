<?php

namespace App\Interfaces;

interface SurveyQuestionRepositoryInterface
{
    public function index();
    public function store(array $details);
    public function getById($id);
    public function update(array $details, $id);
    public function delete($id);
    public function restore($id);
    public function getDeleted();
    public function getAll();
}
