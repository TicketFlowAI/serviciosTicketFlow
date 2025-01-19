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
        try {
            $data = $this->intervalRepositoryInterface->index();
            return ApiResponseClass::sendResponse(IntervalResource::collection($data->load('email')), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve intervals', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/intervals",
     *     summary="Create a new interval",
     *     tags={"Intervals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"days", "type", "email_id"},
     *             @OA\Property(property="days", type="integer", example=7, description="Number of days for the interval"),
     *             @OA\Property(property="type", type="string", example="Weekly", description="Type of the interval"),
     *             @OA\Property(property="email_id", type="integer", example=1, description="ID of the associated email template")
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
            'days' => $request->days,
            'type' => $request->type,
            'email_id' => $request->email_id,
        ];
        DB::beginTransaction();
        try {
            $interval = $this->intervalRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new IntervalResource($interval), 'Interval Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create interval', 500);
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
        try {
            $interval = $this->intervalRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(new IntervalResource($interval->load('email')), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve interval', 500);
        }
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
     *         description="ID of the interval",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"days", "type", "email_id"},
     *             @OA\Property(property="days", type="integer", example=10, description="Updated number of days for the interval"),
     *             @OA\Property(property="type", type="string", example="Monthly", description="Updated type of the interval"),
     *             @OA\Property(property="email_id", type="integer", example=2, description="Updated ID of the associated email template")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Interval updated successfully"),
     *     @OA\Response(response=400, description="Invalid request"),
     *     @OA\Response(response=404, description="Interval not found")
     * )
     */
    public function update(UpdateIntervalRequest $request, $id)
    {
        $updateDetails = [
            'days' => $request->days,
            'type' => $request->type,
            'email_id' => $request->email_id,
        ];
        DB::beginTransaction();
        try {
            $this->intervalRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Interval Update Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update interval', 500);
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
        try {
            $this->intervalRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Interval Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete interval', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/intervals/deleted",
     *     summary="Get a list of deleted intervals",
     *     tags={"Intervals"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted intervals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/IntervalResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->intervalRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(IntervalResource::collection($data->load('email')), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted intervals', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/intervals/{id}/restore",
     *     summary="Restore a deleted interval",
     *     tags={"Intervals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Interval restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/IntervalResource")
     *     ),
     *     @OA\Response(response=404, description="Interval not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->intervalRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('Interval Restore Successful', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore interval', 500);
        }
    }
}
