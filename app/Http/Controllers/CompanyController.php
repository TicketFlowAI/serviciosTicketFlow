<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;
use App\Interfaces\CompanyRepositoryInterface;
use App\Classes\ApiResponseClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Companies",
 *     description="API Endpoints for managing companies"
 * )
 */
class CompanyController extends Controller
{
    private CompanyRepositoryInterface $companyRepositoryInterface;

    public function __construct(CompanyRepositoryInterface $companyRepositoryInterface)
    {
        $this->companyRepositoryInterface = $companyRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/companies",
     *     summary="Get a list of companies",
     *     tags={"Companies"},
     *     @OA\Response(
     *         response=200,
     *         description="List of companies",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CompanyResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->companyRepositoryInterface->index();
        $user = Auth::user();
        if ($user->hasRole('client')) {
            $data = $data->where('id', $user->company_id);
        }
        return ApiResponseClass::sendResponse(CompanyResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/companies",
     *     summary="Create a new company",
     *     tags={"Companies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "idNumber", "contactEmail", "phone", "state", "city", "address"},
     *             @OA\Property(property="name", type="string", example="MindSoft Inc."),
     *             @OA\Property(property="idNumber", type="string", example="123456789"),
     *             @OA\Property(property="contactEmail", type="string", example="contact@mindsoft.com"),
     *             @OA\Property(property="phone", type="string", example="+1-555-1234"),
     *             @OA\Property(property="state", type="string", example="California"),
     *             @OA\Property(property="city", type="string", example="San Francisco"),
     *             @OA\Property(property="address", type="string", example="1234 Market Street")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
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
     * @OA\Get(
     *     path="/companies/{id}",
     *     summary="Get details of a specific company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource")
     *     ),
     *     @OA\Response(response=404, description="Company not found")
     * )
     */
    public function show($id)
    {
        $company = $this->companyRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new CompanyResource($company), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/companies/{id}",
     *     summary="Update a company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *             @OA\Property(property="idNumber", type="string", example="987654321"),
     *             @OA\Property(property="contactEmail", type="string", example="updated@mindsoft.com"),
     *             @OA\Property(property="phone", type="string", example="+1-555-6789"),
     *             @OA\Property(property="state", type="string", example="New York"),
     *             @OA\Property(property="city", type="string", example="Brooklyn"),
     *             @OA\Property(property="address", type="string", example="5678 Main Street")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Company updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
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
     * @OA\Delete(
     *     path="/companies/{id}",
     *     summary="Delete a company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company"
     *     ),
     *     @OA\Response(response=204, description="Company deleted successfully"),
     *     @OA\Response(response=404, description="Company not found"),
     *     @OA\Response(response=400, description="Cannot delete, users or service contracts associated")
     * )
     */
    public function destroy($id)
    {
        // Check for associated users
        $company = $this->companyRepositoryInterface->getById($id);
        if ($company->users()->exists()) {
            return ApiResponseClass::sendResponse(null, 'Cannot delete, users associated', 400);
        }

        // Check for associated service contracts
        if ($company->serviceContract()->exists()) {
            return ApiResponseClass::sendResponse(null, 'Cannot delete, service contracts associated', 400);
        }

        $this->companyRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Company Delete Successful', '', 204);
    }

    /**
     * @OA\Get(
     *     path="/companies/{id}/users",
     *     summary="Get all users from a specific company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Company not found")
     * )
     */
    public function getUsersByCompanyId($id)
    {
        $company = $this->companyRepositoryInterface->getById($id);
        if (!$company) {
            return ApiResponseClass::sendResponse(null, 'Company not found', 404);
        }
        $users = $company->users; // Assuming the Company model has a 'users' relationship
        return ApiResponseClass::sendResponse(UserResource::collection($users), '', 200);
    }

    /**
     * @OA\Get(
     *     path="/companies/deleted",
     *     summary="Get a list of soft-deleted companies",
     *     tags={"Companies"},
     *     @OA\Response(
     *         response=200,
     *         description="List of soft-deleted companies",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CompanyResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        $data = $this->companyRepositoryInterface->getDeleted();
        return ApiResponseClass::sendResponse(CompanyResource::collection($data), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/companies/{id}/restore",
     *     summary="Restore a soft-deleted company",
     *     tags={"Companies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the company"
     *     ),
     *     @OA\Response(response=200, description="Company restored successfully"),
     *     @OA\Response(response=404, description="Company not found")
     * )
     */
    public function restore($id)
    {
        $this->companyRepositoryInterface->restore($id);
        return ApiResponseClass::sendResponse('Company Restore Successful', '', 200);
    }
}
