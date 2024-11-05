<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    
    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->categoryRepositoryInterface->index();

        return ApiResponseClass::sendResponse(CategoryResource::collection($data),'',200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $details =[
            'category' => $request->category
        ];
        DB::beginTransaction();
        try{
             $category = $this->categoryRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new CategoryResource($category),'Category Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = $this->categoryRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new CategoryResource($category),'',200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $updateDetails =[
            'category' => $request->category
        ];
        DB::beginTransaction();
        try{
            $this->categoryRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Category Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->categoryRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Category Delete Successful','',204);
    }
}
