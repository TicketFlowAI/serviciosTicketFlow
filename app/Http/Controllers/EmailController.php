<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Interfaces\EmailRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\EmailResource;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Emails",
 *     description="API Endpoints for managing email templates"
 * )
 */
class EmailController extends Controller
{
    private EmailRepositoryInterface $emailRepositoryInterface;

    public function __construct(EmailRepositoryInterface $emailRepositoryInterface)
    {
        $this->emailRepositoryInterface = $emailRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/emails",
     *     summary="Get a list of email templates",
     *     tags={"Emails"},
     *     @OA\Response(
     *         response=200,
     *         description="List of email templates",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/EmailResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->emailRepositoryInterface->index();
        return ApiResponseClass::sendResponse(EmailResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/emails",
     *     summary="Create a new email template",
     *     tags={"Emails"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "details"},
     *             @OA\Property(property="name", example="Welcome Email"),
     *             @OA\Property(property="details", example="Template details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Email template created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/EmailResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreEmailRequest $request)
    {
        $details = [
            'name' => $request->name,
            'details' => $request->details,
        ];
        DB::beginTransaction();
        try {
            $email = $this->emailRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new EmailResource($email), 'Email Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Get(
     *     path="/emails/{id}",
     *     summary="Get details of a specific email template",
     *     tags={"Emails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the email template"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email template details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/EmailResource")
     *     ),
     *     @OA\Response(response=404, description="Email template not found")
     * )
     */
    public function show($id)
    {
        $email = $this->emailRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new EmailResource($email), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/emails/{id}",
     *     summary="Update an email template",
     *     tags={"Emails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the email template"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "details"},
     *             @OA\Property(property="name", example="Updated Email Template"),
     *             @OA\Property(property="details", example="Updated template details")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Email template updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateEmailRequest $request, $id)
    {
        $updateDetails = [
            'name' => $request->name,
            'details' => $request->details,
        ];
        DB::beginTransaction();
        try {
            $email = $this->emailRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Email Update Successful', '', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * @OA\Delete(
     *     path="/emails/{id}",
     *     summary="Delete an email template",
     *     tags={"Emails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the email template"
     *     ),
     *     @OA\Response(response=204, description="Email template deleted successfully"),
     *     @OA\Response(response=404, description="Email template not found")
     * )
     */
    public function destroy($id)
    {
        $this->emailRepositoryInterface->delete($id);
        return ApiResponseClass::sendResponse('Email Delete Successful', '', 204);
    }
}
