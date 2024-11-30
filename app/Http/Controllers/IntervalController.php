<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Http\Requests\StoreIntervalRequest;
use App\Http\Requests\UpdateIntervalRequest;
use App\Interfaces\IntervalRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\IntervalResource;
use Illuminate\Support\Facades\DB;
class IntervalController extends Controller
{
    
    private IntervalRepositoryInterface $intervalRepositoryInterface;
    
    public function __construct(IntervalRepositoryInterface $intervalRepositoryInterface)
    {
        $this->intervalRepositoryInterface = $intervalRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->intervalRepositoryInterface->index();

        return ApiResponseClass::sendResponse(IntervalResource::collection($data),'',200);
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
    public function store(StoreIntervalRequest $request)
    {
        $details =[
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransaction();
        try{
             $interval = $this->intervalRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new IntervalResource($interval),'Interval Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $interval = $this->intervalRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new IntervalResource($interval),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interval $interval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIntervalRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransaction();
        try{
             $interval = $this->intervalRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Interval Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->intervalRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Interval Delete Successful','',204);
    }
}