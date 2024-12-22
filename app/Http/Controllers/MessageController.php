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

class MessageController extends Controller
{
    private MessageRepositoryInterface $messageRepositoryInterface;
    private TicketRepositoryInterface $ticketRepositoryInterface;

    public function __construct(MessageRepositoryInterface $messageRepositoryInterface, TicketRepositoryInterface $ticketRepositoryInterface)
    {
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $details = [
            'ticket_id' => $request->ticket_id,
            'content' => $request->content,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
            'user_lastname' => Auth::user()->lastname,
            'user_role' => Auth::user()->getRoleNames()->first()
        ];

        DB::beginTransaction();
        try {
            $message = $this->messageRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new MessageResource($message), 'Message Create Successful', 201);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $data = $this->messageRepositoryInterface->getById($id);

        $data->load('user:id,name,lastname');

        $user = $request->user();

        // Update the ticket based on the user's role
        if ($user->hasRole('technician')) {
            $data->first()->ticket->update(['NewClientMessage' => false]);
        } elseif ($user->hasRole('client')) {
            $data->first()->ticket->update(['NewTechnicianMessage' => false]);
        }

        foreach ($data as $message) {
            $message->userRole = $message->user->roles->first()->name;
            
        }
        info($data);
        return ApiResponseClass::sendResponse(MessageResource::collection($data), '', 200);
    }
}
