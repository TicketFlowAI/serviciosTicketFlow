<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Interfaces\CompanyRepositoryInterface;
use App\Classes\ApiResponseClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private CompanyRepositoryInterface $companyRepositoryInterface;

    public function __construct(CompanyRepositoryInterface $companyRepositoryInterface)
    {
        $this->companyRepositoryInterface = $companyRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->companyRepositoryInterface->index();

        // Ensure client users only see their own company
        $user = Auth::user();
        if ($user->hasRole('client')) {
            $data = $data->where('id', $user->company_id);
        }

        return ApiResponseClass::sendResponse(CompanyResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $details = $request->only([
            'name', 'idNumber', 'contactEmail', 'phone', 'state', 'city', 'address'
        ]);

        DB::beginTransaction();
        try {
            $company = $this->companyRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new CompanyResource($company), 'Company Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $company = $this->companyRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new CompanyResource($company), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, $id)
    {
        $updateDetails = $request->validated();

        DB::beginTransaction();
        try {
            $this->companyRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Company Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->companyRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Company Delete Successful', '', 204);
    }
}
