<?php

namespace App\Http\Controllers;

use App\Models\Interval;
use App\Http\Requests\StoreIntervalRequest;
use App\Http\Requests\UpdateIntervalRequest;
use App\Interfaces\IntervalRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\IntervalResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Intervals",
 *     description="API Endpoints for managing intervals"
 * )
 */
class IntervalController extends Controller
{
    private IntervalRepositoryInterface $intervalRepositoryInterface;

    public function __construct(IntervalRepositoryInterface $intervalRepositoryInterface)
    {
        $this->intervalRepositoryInterface = $intervalRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/intervals",
     *     summary="Get a list of intervals",
     *     tags={"Intervals"},
     *     @OA\Response(
     *         response=200,
     *         description="List of intervals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/IntervalResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->intervalRepositoryInterface->index();
        return ApiResponseClass::sendResponse(IntervalResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/intervals",
     *     summary="Create a new interval",
     *     tags={"Intervals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "details"},
     *             @OA\Property(property="name", example="Weekly"),
     *             @OA\Property(property="details", example="Every 7 days")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Interval created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/IntervalResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreIntervalRequest $request)
    {
        $details = [
            'name' => $request->name,
            'details' => $request->details,
        ];
        DB::beginTransaction();
        try {
            $interval = $this->intervalRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new IntervalResource($interval), 'Interval Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/intervals/{id}",
     *     summary="Get details of a specific interval",
     *     tags={"Intervals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Interval details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/IntervalResource")
     *     ),
     *     @OA\Response(response=404, description="Interval not found")
     * )
     */
    public function show($id)
    {
        $interval = $this->intervalRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new IntervalResource($interval), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/intervals/{id}",
     *     summary="Update an interval",
     *     tags={"Intervals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "details"},
     *             @OA\Property(property="name", example="Updated Interval"),
     *             @OA\Property(property="details", example="Updated details")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Interval updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateIntervalRequest $request, $id)
    {
        $updateDetails = [
            'name' => $request->name,
            'details' => $request->details,
        ];
        DB::beginTransaction();
        try {
            $interval = $this->intervalRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Interval Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/intervals/{id}",
     *     summary="Delete an interval",
     *     tags={"Intervals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval"
     *     ),
     *     @OA\Response(response=204, description="Interval deleted successfully"),
     *     @OA\Response(response=404, description="Interval not found")
     * )
     */
    public function destroy($id)
    {
        $this->intervalRepositoryInterface->delete($id);
        return ApiResponseClass::sendResponse('Interval Delete Successful', '', 204);
    }
}
