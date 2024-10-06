<?php

namespace App\Http\Controllers;

use App\Models\ServiceTerm;
use App\Http\Requests\StoreServiceTermRequest;
use App\Http\Requests\UpdateServiceTermRequest;
use App\Interfaces\ServiceTermRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceTermResource;
use Illuminate\Support\Facades\DB;
class ServiceTermController extends Controller
{
    
    private ServiceTermRepositoryInterface $serviceTermRepositoryInterface;
    
    public function __construct(ServiceTermRepositoryInterface $serviceTermRepositoryInterface)
    {
        $this->serviceTermRepositoryInterface = $serviceTermRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->serviceTermRepositoryInterface->index();

        return ApiResponseClass::sendResponse(ServiceTermResource::collection($data),'',200);
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
    public function store(StoreServiceTermRequest $request)
    {
        $details =[
            'term' => $request->term,
            'months' => $request->months
        ];
        DB::beginTransaction();
        try{
             $serviceTerm = $this->serviceTermRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new ServiceTermResource($serviceTerm),'ServiceTerm Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $serviceTerm = $this->serviceTermRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new ServiceTermResource($serviceTerm),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceTerm $serviceTerm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceTermRequest $request, $id)
    {
        $updateDetails =[
            'term' => $request->term,
            'months' => $request->months
        ];
        DB::beginTransaction();
        try{
             $serviceTerm = $this->serviceTermRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('ServiceTerm Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->serviceTermRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('ServiceTerm Delete Successful','',204);
    }
}