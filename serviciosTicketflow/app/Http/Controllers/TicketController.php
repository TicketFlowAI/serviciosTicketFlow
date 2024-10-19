<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    private TicketRepositoryInterface $ticketRepositoryInterface;
    
    public function __construct(TicketRepositoryInterface $ticketRepositoryInterface)
    {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->ticketRepositoryInterface->index();

        return ApiResponseClass::sendResponse(TicketResource::collection($data),'',200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $details =[
            'service_contract_id'=> $request->service_contract_id,
            'title'=> $request->title,
            'priority'=> $request->priority,
            'needsHumanInteraction'=> $request->needsHumanInteraction,
            'complexity'=> $request->complexity,
            'user_id'=> $request->user_id
        ];
        DB::beginTransaction();
        try{
             $ticket = $this->ticketRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new TicketResource($ticket),'Ticket Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = $this->ticketRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new TicketResource($ticket),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $id)
    {
        $updateDetails =[
            'service_contract_id'=> $request->service_contract_id,
            'title'=> $request->title,
            'priority'=> $request->priority,
            'needsHumanInteraction'=> $request->needsHumanInteraction,
            'complexity'=> $request->complexity,
            'user_id'=> $request->user_id
        ];
        DB::beginTransaction();
        try{
             $ticket = $this->ticketRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Ticket Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->ticketRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Ticket Delete Successful','',204);
    }
}