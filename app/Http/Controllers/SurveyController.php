<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
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
     *             required={"SurveyAnswers"},
     *             @OA\Property(
     *                 property="SurveyAnswers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="ticket_id", type="integer", example=1),
     *                     @OA\Property(property="question_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="score", type="integer", example=5)
     *                 )
     *             )
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
        DB::beginTransaction();

        try {
            $surveyAnswers = $request->input('SurveyAnswers');

            foreach ($surveyAnswers as $answer) {
                $details = [
                    'ticket_id' => $answer['ticket_id'],
                    'question_id' => $answer['question_id'],
                    'user_id' => $answer['user_id'],
                    'score' => $answer['score'],
                ];

                $this->surveyRepositoryInterface->store($details);
            }

            DB::commit();
            return ApiResponseClass::sendResponse(null, 'Survey answers stored successfully', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create survey answers', 500);
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
        // try {
            $survey = $this->surveyRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(SurveyResource::collection($survey), '', 200);
        // } catch (\Exception $ex) {
        //     return ApiResponseClass::sendResponse(null, 'Failed to retrieve survey', 500);
        // }
    }
}
