<?php

namespace App\Http\Controllers;

use App\Models\ServiceTerm;
use App\Http\Requests\StoreServiceTermRequest;
use App\Http\Requests\UpdateServiceTermRequest;
use App\Interfaces\ServiceTermRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceTermResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Service Terms",
 *     description="API Endpoints for managing service terms"
 * )
 */
class ServiceTermController extends Controller
{
    private ServiceTermRepositoryInterface $serviceTermRepositoryInterface;

    public function __construct(ServiceTermRepositoryInterface $serviceTermRepositoryInterface)
    {
        $this->serviceTermRepositoryInterface = $serviceTermRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/service-terms",
     *     summary="Get a list of service terms",
     *     tags={"Service Terms"},
     *     @OA\Response(
     *         response=200,
     *         description="List of service terms",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceTermResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->serviceTermRepositoryInterface->index();
        return ApiResponseClass::sendResponse(ServiceTermResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/service-terms",
     *     summary="Create a new service term",
     *     tags={"Service Terms"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"term", "months"},
     *             @OA\Property(property="term", type="string", example="Annual"),
     *             @OA\Property(property="months", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service term created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceTermResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreServiceTermRequest $request)
    {
        $details = [
            'term' => $request->term,
            'months' => $request->months,
        ];
        DB::beginTransaction();
        try {
            $serviceTerm = $this->serviceTermRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new ServiceTermResource($serviceTerm), 'ServiceTerm Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-terms/{id}",
     *     summary="Get details of a specific service term",
     *     tags={"Service Terms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service term",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service term details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceTermResource")
     *     ),
     *     @OA\Response(response=404, description="Service term not found")
     * )
     */
    public function show($id)
    {
        $serviceTerm = $this->serviceTermRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new ServiceTermResource($serviceTerm), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/service-terms/{id}",
     *     summary="Update a service term",
     *     tags={"Service Terms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service term",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"term", "months"},
     *             @OA\Property(property="term", type="string", example="Updated Term"),
     *             @OA\Property(property="months", type="integer", example=6)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Service term updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateServiceTermRequest $request, $id)
    {
        $updateDetails = [
            'term' => $request->term,
            'months' => $request->months,
        ];
        DB::beginTransaction();
        try {
            $serviceTerm = $this->serviceTermRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('ServiceTerm Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/service-terms/{id}",
     *     summary="Delete a service term",
     *     tags={"Service Terms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service term",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Service term deleted successfully"),
     *     @OA\Response(response=404, description="Service term not found")
     * )
     */
    public function destroy($id)
    {
        $this->serviceTermRepositoryInterface->delete($id);
        return ApiResponseClass::sendResponse('ServiceTerm Delete Successful', '', 204);
    }
}
