<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Interfaces\ServiceRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\DB;
class ServiceController extends Controller
{
    private ServiceRepositoryInterface $serviceRepositoryInterface;

    public function __construct(ServiceRepositoryInterface $serviceRepositoryInterface)
    {
        $this->serviceRepositoryInterface = $serviceRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->serviceRepositoryInterface->index();
        $data->load('tax:id,description','category:id,category');
        
        return ApiResponseClass::sendResponse(ServiceResource::collection($data), '', 200);
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
    public function store(StoreServiceRequest $request)
    {
        $details = [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id
        ];
        DB::beginTransaction();
        try {
            $service = $this->serviceRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new ServiceResource($service), 'Service Create Successful', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->serviceRepositoryInterface->getById($id);
        $data->load('tax:id,description','category:id,category');

        return ApiResponseClass::sendResponse(new ServiceResource($data), '', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        $updateDetails = [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id
        ];
        DB::beginTransaction();
        try {
            $service = $this->serviceRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Service Update Successful', '', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->serviceRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Service Delete Successful', '', 204);
    }
}
