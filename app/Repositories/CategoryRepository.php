<?php

namespace App\Repositories;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieves all items.
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Retrieves an item by ID.
     */
    public function getById($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Creates a new item in the repository.
     */
    public function store(array $data)
    {
        return Category::create($data);
    }

    /**
     * Updates an Item by ID.
     */
    public function update(array $data, $id)
    {
        return Category::whereId($id)->update($data);
    }

    /**
     * Deletes an Item by ID.
     */
    public function delete($id)
    {
        return Category::destroy($id) > 0;
    }

    /**
     * Retrieves all soft deleted items.
     */
    public function getDeleted()
    {
        return Category::onlyTrashed()->get();
    }

    /**
     * Restores a soft deleted item by ID.
     */
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        return $category;
    }
}
