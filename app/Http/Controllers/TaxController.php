<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Interfaces\TaxRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\TaxResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Taxes",
 *     description="API Endpoints for managing taxes"
 * )
 */
class TaxController extends Controller
{
    private TaxRepositoryInterface $taxRepositoryInterface;

    public function __construct(TaxRepositoryInterface $taxRepositoryInterface)
    {
        $this->taxRepositoryInterface = $taxRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/taxes",
     *     summary="Get a list of taxes",
     *     tags={"Taxes"},
     *     @OA\Response(
     *         response=200,
     *         description="List of taxes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TaxResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->taxRepositoryInterface->index();

        return ApiResponseClass::sendResponse(TaxResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/taxes",
     *     summary="Create a new tax",
     *     tags={"Taxes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "value"},
     *             @OA\Property(property="description", type="string", example="Value Added Tax (VAT)"),
     *             @OA\Property(property="value", type="number", format="float", example=15.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tax created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaxResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreTaxRequest $request)
    {
        $details = [
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try {
            $tax = $this->taxRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new TaxResource($tax), 'Tax Create Successful', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/taxes/{id}",
     *     summary="Get details of a specific tax",
     *     tags={"Taxes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tax",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tax details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaxResource")
     *     ),
     *     @OA\Response(response=404, description="Tax not found")
     * )
     */
    public function show($id)
    {
        $tax = $this->taxRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new TaxResource($tax), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/taxes/{id}",
     *     summary="Update a tax",
     *     tags={"Taxes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tax",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "value"},
     *             @OA\Property(property="description", type="string", example="Updated Tax Description"),
     *             @OA\Property(property="value", type="number", format="float", example=18.0)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tax updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateTaxRequest $request, $id)
    {
        $updateDetails = [
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try {
            $this->taxRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Tax Update Successful', '', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/taxes/{id}",
     *     summary="Delete a tax",
     *     tags={"Taxes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tax",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Tax deleted successfully"),
     *     @OA\Response(response=404, description="Tax not found")
     * )
     */
    public function destroy($id)
    {
        $this->taxRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Tax Delete Successful', '', 204);
    }
}
