<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Interfaces\SurveyRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\SurveyResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Surveys",
 *     description="API Endpoints for managing surveys"
 * )
 */
class SurveyController extends Controller
{
    private SurveyRepositoryInterface $surveyRepositoryInterface;

    public function __construct(SurveyRepositoryInterface $surveyRepositoryInterface)
    {
        $this->surveyRepositoryInterface = $surveyRepositoryInterface;
    }

    /**
     * @OA\Post(
     *     path="/surveys",
     *     summary="Create a new survey",
     *     tags={"Surveys"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ticket_id", "question_id", "user_id", "score"},
     *             @OA\Property(property="ticket_id", type="integer", example=1),
     *             @OA\Property(property="question_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="score", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Survey created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Survey")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreSurveyRequest $request)
    {
        $details = $request->only(['ticket_id', 'question_id', 'user_id', 'score']);
        DB::beginTransaction();
        try {
            $survey = $this->surveyRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new SurveyResource($survey), 'Survey Create Successful', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/surveys/{id}",
     *     summary="Get details of a specific survey",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Survey details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Survey")
     *     ),
     *     @OA\Response(response=404, description="Survey not found")
     * )
     */
    public function show($id)
    {
        $survey = $this->surveyRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(SurveyResource::collection($survey), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/surveys/{id}",
     *     summary="Update a survey",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ticket_id", "question_id", "user_id", "score"},
     *             @OA\Property(property="ticket_id", type="integer", example=1),
     *             @OA\Property(property="question_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="score", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Survey updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateSurveyRequest $request, $id)
    {
        $updateDetails = $request->only(['ticket_id', 'question_id', 'user_id', 'score']);
        DB::beginTransaction();
        try {
            $this->surveyRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Survey Update Successful', '', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/surveys/{id}",
     *     summary="Delete a survey",
     *     tags={"Surveys"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Survey deleted successfully"),
     *     @OA\Response(response=404, description="Survey not found")
     * )
     */
    public function destroy($id)
    {
        $this->surveyRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Survey Delete Successful', '', 204);
    }
}
