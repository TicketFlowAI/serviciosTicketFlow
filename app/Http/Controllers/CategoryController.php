<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Category",
 *     description="Endpoints for managing categories"
 * )
 */
class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = $this->categoryRepositoryInterface->index();
        return ApiResponseClass::sendResponse(CategoryResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Category"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category"},
     *             @OA\Property(property="category", type="string", example="Electronics")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $details = [
            'category' => $request->category,
        ];
        DB::beginTransaction();
        try {
            $category = $this->categoryRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new CategoryResource($category), 'Category Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a specific category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     )
     * )
     */
    public function show($id)
    {
        $category = $this->categoryRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new CategoryResource($category), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category"},
     *             @OA\Property(property="category", type="string", example="Updated Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category updated successfully"
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $updateDetails = [
            'category' => $request->category,
        ];
        DB::beginTransaction();
        try {
            $this->categoryRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Category Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted successfully"
     *     ),
     *     @OA\Response(response=400, description="Cannot delete, services associated")
     * )
     */
    public function destroy($id)
    {
        // Check for associated services
        $category = $this->categoryRepositoryInterface->getById($id);
        if ($category->services()->exists()) {
            return ApiResponseClass::sendResponse(null, 'Cannot delete, services associated', 400);
        }

        $this->categoryRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Category Delete Successful', '', 204);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/deleted",
     *     summary="Get all soft deleted categories",
     *     tags={"Category"},
     *     @OA\Response(
     *         response=200,
     *         description="List of soft deleted categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CategoryResource")
     *         )
     *     )
     * )
     */
    public function getDeleted()
    {
        $data = $this->categoryRepositoryInterface->getDeleted();
        return ApiResponseClass::sendResponse(CategoryResource::collection($data), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}/restore",
     *     summary="Restore a soft deleted category",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
     *     )
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse(new CategoryResource($category), 'Category Restore Successful', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }
}
