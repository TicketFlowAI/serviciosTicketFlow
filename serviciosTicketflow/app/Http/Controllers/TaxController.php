<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Interfaces\TaxRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\TaxResource;
use Illuminate\Support\Facades\DB;
class TaxController extends Controller
{
    
    private TaxRepositoryInterface $taxRepositoryInterface;
    
    public function __construct(TaxRepositoryInterface $taxRepositoryInterface)
    {
        $this->taxRepositoryInterface = $taxRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->taxRepositoryInterface->index();

        return ApiResponseClass::sendResponse(TaxResource::collection($data),'',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxRequest $request)
    {
        $details =[
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try{
             $tax = $this->taxRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new TaxResource($tax),'Tax Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tax = $this->taxRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new TaxResource($tax),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxRequest $request, $id)
    {
        $updateDetails =[
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try{
             $tax = $this->taxRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Tax Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->taxRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Tax Delete Successful','',204);
    }
}