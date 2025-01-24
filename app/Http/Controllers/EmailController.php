<?php

namespace App\Http\Controllers;

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
        try {
            $data = $this->emailRepositoryInterface->index();
            return ApiResponseClass::sendResponse(EmailResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve email templates', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/emails",
     *     summary="Create a new email template",
     *     tags={"Emails"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"template_name", "subject", "body"},
     *             @OA\Property(property="template_name", type="string", example="Welcome Email", description="The name of the email template"),
     *             @OA\Property(property="subject", type="string", example="Welcome to Our Service", description="The subject of the email template"),
     *             @OA\Property(property="body", type="string", example="<p>Dear User, Welcome to our service!</p>", description="The body content of the email template")
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
            'template_name' => $request->template_name,
            'subject' => $request->subject,
            'body' => $request->body,
        ];
        DB::beginTransaction();
        try {
            $email = $this->emailRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new EmailResource($email), 'Email Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create email template', 500);
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
        try {
            $email = $this->emailRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(new EmailResource($email), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve email template', 500);
        }
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
     *         description="ID of the email template",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"template_name", "subject", "body"},
     *             @OA\Property(property="template_name", type="string", example="Updated Email Template", description="Updated name of the email template"),
     *             @OA\Property(property="subject", type="string", example="Updated Subject", description="Updated subject of the email template"),
     *             @OA\Property(property="body", type="string", example="<p>Updated email body content.</p>", description="Updated HTML body of the email template")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Email template updated successfully"),
     *     @OA\Response(response=400, description="Invalid request"),
     *     @OA\Response(response=404, description="Email template not found")
     * )
     */
    public function update(UpdateEmailRequest $request, $id)
    {
        $updateDetails = [
            'template_name' => $request->template_name,
            'subject' => $request->subject,
            'body' => $request->body,
        ];
        DB::beginTransaction();
        try {
            $this->emailRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse('Email Update Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update email template', 500);
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
     *     @OA\Response(response=404, description="Email template not found"),
     *     @OA\Response(response=400, description="Cannot delete, intervals associated")
     * )
     */
    public function destroy($id)
    {
        try {
            // Check for associated intervals
            $email = $this->emailRepositoryInterface->getById($id);
            if ($email->interval()->exists()) {
                return ApiResponseClass::sendResponse(null, 'Cannot delete, intervals associated', 400);
            }

            $this->emailRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Email Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete email template', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/emails/deleted",
     *     summary="Get a list of deleted email templates",
     *     tags={"Emails"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted email templates",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/EmailResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $data = $this->emailRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(EmailResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted email templates', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/emails/{id}/restore",
     *     summary="Restore a deleted email template",
     *     tags={"Emails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the email template"
     *     ),
     *     @OA\Response(response=200, description="Email template restored successfully"),
     *     @OA\Response(response=404, description="Email template not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->emailRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('Email Restore Successful', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore email template', 500);
        }
    }
}
