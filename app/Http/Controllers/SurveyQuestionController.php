<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyQuestionRequest;
use App\Http\Requests\UpdateSurveyQuestionRequest;
use App\Models\SurveyQuestion;
use App\Interfaces\SurveyQuestionRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\SurveyQuestionResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="SurveyQuestions",
 *     description="API Endpoints for managing survey questions"
 * )
 */
class SurveyQuestionController extends Controller
{
    private SurveyQuestionRepositoryInterface $surveyQuestionRepositoryInterface;

    public function __construct(SurveyQuestionRepositoryInterface $surveyQuestionRepositoryInterface)
    {
        $this->surveyQuestionRepositoryInterface = $surveyQuestionRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/survey-questions",
     *     summary="Get a list of survey questions",
     *     tags={"SurveyQuestions"},
     *     @OA\Response(
     *         response=200,
     *         description="List of survey questions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SurveyQuestionResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        try {
            $data = $this->surveyQuestionRepositoryInterface->index();
            return ApiResponseClass::sendResponse(SurveyQuestionResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve survey questions', 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/survey-questions",
     *     summary="Create a new survey question",
     *     tags={"SurveyQuestions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", example="¿Cómo calificaría la satisfacción con el servicio de soporte recibido?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Survey question created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SurveyQuestionResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreSurveyQuestionRequest $request)
    {
        $details = ['question' => $request->question, 'status' => $request->status];
        DB::beginTransaction();
        try {
            $surveyQuestion = $this->surveyQuestionRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new SurveyQuestionResource($surveyQuestion), 'Survey Question Created Successfully', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create survey question', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/survey-questions/{id}",
     *     summary="Get details of a specific survey question",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Survey question details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SurveyQuestionResource")
     *     ),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function show($id)
    {
        try {
            $surveyQuestion = $this->surveyQuestionRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(new SurveyQuestionResource($surveyQuestion), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve survey question', 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurveyQuestion $surveyQuestion)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/survey-questions/{id}",
     *     summary="Update a survey question",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", example="Updated survey question")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Survey question updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
    public function update(UpdateSurveyQuestionRequest $request, $id)
    {
        $updateDetails = ['question' => $request->question, 'status' => $request->status];
        DB::beginTransaction();
        try {
            $this->surveyQuestionRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Survey Question Updated Successfully', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update survey question', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/survey-questions/{id}",
     *     summary="Delete a survey question",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Survey question deleted successfully"),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $this->surveyQuestionRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Survey Question Deleted Successfully', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete survey question', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/survey-questions/deleted",
     *     summary="Get a list of deleted survey questions",
     *     tags={"SurveyQuestions"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted survey questions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SurveyQuestionResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->surveyQuestionRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(SurveyQuestionResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted survey questions', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/survey-questions/{id}/restore",
     *     summary="Restore a deleted survey question",
     *     tags={"SurveyQuestions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the survey question",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Survey question restored successfully"),
     *     @OA\Response(response=404, description="Survey question not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->surveyQuestionRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('Survey Question Restored Successfully', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore survey question', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/survey-questions/all",
     *     summary="Get a list of all survey questions",
     *     tags={"SurveyQuestions"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all survey questions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SurveyQuestionResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getAll()
    {
        try {
            $data = $this->surveyQuestionRepositoryInterface->getAll();
            return ApiResponseClass::sendResponse(SurveyQuestionResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve all survey questions', 500);
        }
    }
}
