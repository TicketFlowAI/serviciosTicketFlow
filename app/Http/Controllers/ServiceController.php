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
        try {
            $data = $this->serviceRepositoryInterface->index();
            $data->load('tax:id,description', 'category:id,category');

            return ApiResponseClass::sendResponse(ServiceResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve services', 500);
        }
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
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create service', 500);
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
        try {
            $data = $this->serviceRepositoryInterface->getById($id);
            $data->load('tax:id,description', 'category:id,category');

            return ApiResponseClass::sendResponse(new ServiceResource($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve service', 500);
        }
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
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update service', 500);
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
     *     @OA\Response(response=404, description="Service not found"),
     *     @OA\Response(response=400, description="Cannot delete, service contracts associated")
     * )
     */
    public function destroy($id)
    {
        try {
            // Check for associated service contracts
            $service = $this->serviceRepositoryInterface->getById($id);
            if ($service->serviceContract()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete, service contracts associated', 400);
            }

            $this->serviceRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Service Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete service', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/services/deleted",
     *     summary="Get a list of deleted services",
     *     tags={"Services"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted services",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ServiceResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->serviceRepositoryInterface->getDeleted();
            $data->load('tax:id,description', 'category:id,category');

            return ApiResponseClass::sendResponse(ServiceResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted services', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/services/{id}/restore",
     *     summary="Restore a deleted service",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Service restored successfully"),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->serviceRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('Service Restore Successful', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore service', 500);
        }
    }
}
