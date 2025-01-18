<?php

namespace App\Repositories;

use App\Interfaces\SurveyQuestionRepositoryInterface;
use App\Models\SurveyQuestion;

class SurveyQuestionRepository implements SurveyQuestionRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieves all active items.
     */
    public function index()
    {
        return SurveyQuestion::where('active', true)->get();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return SurveyQuestion::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return SurveyQuestion::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return SurveyQuestion::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        SurveyQuestion::destroy($id);
    }

    /**
     * Retrieves all deleted items.
     */
    public function getDeleted()
    {
        return SurveyQuestion::onlyTrashed()->get();
    }

    /**
     * Restores a deleted item by ID.
     */
    public function restore($id)
    {
        return SurveyQuestion::withTrashed()->where('id', $id)->restore();
    }

    /**
     * Retrieves all items, both active and inactive.
     */
    public function getAll()
    {
        return SurveyQuestion::all();
    }
}
