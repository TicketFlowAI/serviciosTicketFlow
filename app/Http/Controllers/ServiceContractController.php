<?php

namespace App\Http\Controllers;

use App\Models\ServiceContract;
use App\Http\Requests\StoreServiceContractRequest;
use App\Http\Requests\UpdateServiceContractRequest;
use App\Interfaces\ServiceContractRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceContractResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServiceContractController extends Controller
{
    private ServiceContractRepositoryInterface $serviceContractRepositoryInterface;

    public function __construct(ServiceContractRepositoryInterface $serviceContractRepositoryInterface)
    {
        $this->serviceContractRepositoryInterface = $serviceContractRepositoryInterface;
    }

    /**
     * Verify that the client user can access the resource.
     */
    private function verifyCompanyAccess($resourceCompanyId)
    {
        $user = Auth::user();

        // If user has role 'client' and the company's ID doesn't match, deny access
        if ($user->hasRole('client') && $user->company_id !== $resourceCompanyId) {
            abort(403, 'Unauthorized access to resource.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Filter data based on company for client users
        if ($user->hasRole('client')) {
            $data = $this->serviceContractRepositoryInterface->getContractsByCompany($user->company_id);
        } else {
            $data = $this->serviceContractRepositoryInterface->index();
        }

        $data->load('company:id,name', 'service:id,description,price', 'service_term:id,months,term');
        
        foreach ($data as $serviceContract) {
            $serviceContract->price = $serviceContract->service->price / $serviceContract->service_term->months;
        }

        return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceContractRequest $request)
    {
        $user = Auth::user();

        $details = [
            'company_id' => $user->hasRole('client') ? $user->company_id : $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id,
        ];

        DB::beginTransaction();
        try {
            $serviceContract = $this->serviceContractRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract), 'ServiceContract Create Successful', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $serviceContract = $this->serviceContractRepositoryInterface->getById($id);

        // Verify that the user has access to this resource
        $this->verifyCompanyAccess($serviceContract->company_id);

        $serviceContract->load('company:id,name', 'service:id,description', 'service_term:id,months,term');

        return ApiResponseClass::sendResponse(new ServiceContractResource($serviceContract), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceContractRequest $request, $id)
    {
        $serviceContract = $this->serviceContractRepositoryInterface->getById($id);

        // Verify that the user has access to this resource
        $this->verifyCompanyAccess($serviceContract->company_id);

        $updateDetails = [
            'company_id' => $request->company_id,
            'service_id' => $request->service_id,
            'service_term_id' => $request->service_term_id,
        ];

        DB::beginTransaction();
        try {
            $serviceContract = $this->serviceContractRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('ServiceContract Update Successful', '', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $serviceContract = $this->serviceContractRepositoryInterface->getById($id);

        // Verify that the user has access to this resource
        $this->verifyCompanyAccess($serviceContract->company_id);

        $this->serviceContractRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('ServiceContract Delete Successful', '', 204);
    }

    /**
     * Display contracts by company.
     */
    public function getContractsByCompany($id)
    {
        $user = Auth::user();

        // Verify that the user is accessing their own company's contracts if they are a client
        if ($user->hasRole('client') && $user->company_id != $id) {
            abort(403, 'Unauthorized access to resource.');
        }

        $data = $this->serviceContractRepositoryInterface->getContractsByCompany($id);

        return ApiResponseClass::sendResponse(ServiceContractResource::collection($data), '', 200);
    }
}
