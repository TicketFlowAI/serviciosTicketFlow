<?php

namespace App\Http\Controllers;

use App\Interfaces\TicketRepositoryInterface;
use App\Interfaces\MessageRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Messages",
 *     description="API Endpoints for managing ticket messages"
 * )
 */
class MessageController extends Controller
{
    private MessageRepositoryInterface $messageRepositoryInterface;
    private TicketRepositoryInterface $ticketRepositoryInterface;

    public function __construct(
        MessageRepositoryInterface $messageRepositoryInterface,
        TicketRepositoryInterface $ticketRepositoryInterface
    ) {
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * @OA\Post(
     *     path="/messages",
     *     summary="Create a new message for a ticket",
     *     tags={"Messages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ticket_id", "content"},
     *             @OA\Property(property="ticket_id", example=123),
     *             @OA\Property(property="content", example="This is a message content.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreMessageRequest $request)
    {
        $details = [
            'ticket_id' => $request->ticket_id,
            'content' => $request->content,
            'user_id' => $request->user_id ?? Auth::user()->id,
            'user_name' => $request->user_name ?? Auth::user()->name,
            'user_lastname' => $request->user_lastname ?? Auth::user()->lastname,
            'user_role' => $request->user_role ?? Auth::user()->getRoleNames()->first()
        ];

        DB::beginTransaction();
        try {
            $message = $this->messageRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new MessageResource($message), 'Message Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create message', 500);
        }
    }

    /**
     * Create a message with given ticket_id and content.
     *
     * @param int $ticket_id
     * @param string $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAIMessage($ticket_id, $content)
    {
        $user = \App\Models\User::where('email', 'noreply@mindsoft.biz')->first();
        if (!$user) {
            return Log::error('User not found. You need to create a user matching the line above this one in Message contorller');
        }
        $request = new StoreMessageRequest();
        $request->merge([
            'ticket_id' => $ticket_id,
            'content' => $content,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_lastname' => $user->lastname,
            'user_role' => $user->getRoleNames()->first()
        ]);

        return $this->store($request);
    }

    /**
     * @OA\Get(
     *     path="/messages/{id}",
     *     summary="Get messages for a specific ticket",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the message",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message details retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MessageResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Message not found")
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $data = $this->messageRepositoryInterface->getById($id);

            $data->load('user:id,name,lastname');

            $user = $request->user();
            if ($user->hasRole('technician') || $user->hasRole('super-admin')) {
                $data->first()->ticket->update(['newClientMessage' => false]);
            } elseif ($user->hasRole('client')) {
                $data->first()->ticket->update(['newTechnicianMessage' => false]);
            }

            foreach ($data as $message) {
                $message->userRole = $message->user->roles->first()->name;
            }

            return ApiResponseClass::sendResponse(MessageResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve messages', 500);
        }
    }


}
