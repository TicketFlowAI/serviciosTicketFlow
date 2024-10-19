<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Interfaces\MessageRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    private MessageRepositoryInterface $messageRepositoryInterface;
    
    public function __construct(MessageRepositoryInterface $messageRepositoryInterface)
    {
        $this->messageRepositoryInterface = $messageRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->messageRepositoryInterface->index();

        return ApiResponseClass::sendResponse(MessageResource::collection($data),'',200);
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
    public function store(StoreMessageRequest $request)
    {
        $details =[
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try{
             $message = $this->messageRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new MessageResource($message),'Message Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $message = $this->messageRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new MessageResource($message),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, $id)
    {
        $updateDetails =[
            'description' => $request->description,
            'value' => $request->value
        ];
        DB::beginTransaction();
        try{
             $message = $this->messageRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Message Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->messageRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Message Delete Successful','',204);
    }
}
