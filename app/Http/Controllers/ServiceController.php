<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Interfaces\ServiceRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for managing services"
 * )
 */
class ServiceController extends Controller
{
    private ServiceRepositoryInterface $serviceRepositoryInterface;

    public function __construct(ServiceRepositoryInterface $serviceRepositoryInterface)
    {
        $this->serviceRepositoryInterface = $serviceRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/services",
     *     summary="Get a list of services",
     *     tags={"Services"},
     *     @OA\Response(
     *         response=200,
     *         description="List of services",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->serviceRepositoryInterface->index();
        $data->load('tax:id,description', 'category:id,category');

        return ApiResponseClass::sendResponse(ServiceResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/services",
     *     summary="Create a new service",
     *     tags={"Services"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "description", "price", "tax_id", "details"},
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Premium Support Service"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="tax_id", type="integer", example=3),
     *             @OA\Property(property="details", type="string", example="This service includes 24/7 support.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        $details = [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id,
            'details' => $request->details,
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
     * @OA\Get(
     *     path="/services/{id}",
     *     summary="Get details of a specific service",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ServiceResource")
     *     ),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function show($id)
    {
        $data = $this->serviceRepositoryInterface->getById($id);
        $data->load('tax:id,description', 'category:id,category');

        return ApiResponseClass::sendResponse(new ServiceResource($data), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/services/{id}",
     *     summary="Update a service",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "description", "price", "tax_id", "details"},
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Updated Support Service"),
     *             @OA\Property(property="price", type="number", format="float", example=89.99),
     *             @OA\Property(property="tax_id", type="integer", example=2),
     *             @OA\Property(property="details", type="string", example="This service includes 24/7 support.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Service updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        $updateDetails = [
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'tax_id' => $request->tax_id,
            'details' => $request->details,
        ];
        DB::beginTransaction();
        try {
            $this->serviceRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Service Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/services/{id}",
     *     summary="Delete a service",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Service deleted successfully"),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function destroy($id)
    {
        $this->serviceRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Service Delete Successful', '', 204);
    }
}
