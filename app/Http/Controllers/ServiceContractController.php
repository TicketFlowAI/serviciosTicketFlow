<?php

namespace App\Http\Controllers;

use App\Models\ServiceContract;
use App\Http\Requests\StoreServiceContractRequest;
use App\Http\Requests\UpdateServiceContractRequest;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceContractResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
class ServiceContractController extends Controller
{
    private ServiceContractRepositoryInterface $serviceContractRepositoryInterface;
    
    public function __construct(ServiceContractRepositoryInterface $serviceContractRepositoryInterface)
    {
        $this->serviceContractRepositoryInterface = $serviceContractRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->serviceContractRepositoryInterface->index();

        return ApiResponseClass::sendResponse(ServiceContractResource::collection($data),'',200);
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
    public function store(StoreServiceContractRequest $request)
    {
        $details =[
            'company_id' => $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id
        ];
        DB::beginTransaction();
        try{
             $serviceContract = $this->serviceContractRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract),'ServiceContract Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $serviceContract = $this->serviceContractRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceContract $serviceContract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceContractRequest $request, $id)
    {
        $updateDetails =[
            'company_id' => $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id
        ];
        DB::beginTransaction();
        try{
             $serviceContract = $this->serviceContractRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('ServiceContract Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->serviceContractRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('ServiceContract Delete Successful','',204);
    }

    public function getContractsByCompany($id)
    {
        $serviceContracts = ServiceContract::with(['company:id,name' => function (Builder $query) use($id)  {
            $query->where('company_id', 'like', $id);
        }])->get();

        return ApiResponseClass::sendResponse(ServiceContractResource::collection($serviceContracts),'',200);
    }
    
}
