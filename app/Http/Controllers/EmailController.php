<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Interfaces\EmailRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\EmailResource;
use Illuminate\Support\Facades\DB;
class EmailController extends Controller
{
    
    private EmailRepositoryInterface $emailRepositoryInterface;
    
    public function __construct(EmailRepositoryInterface $emailRepositoryInterface)
    {
        $this->emailRepositoryInterface = $emailRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->emailRepositoryInterface->index();

        return ApiResponseClass::sendResponse(EmailResource::collection($data),'',200);
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
    public function store(StoreEmailRequest $request)
    {
        $details =[
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransaction();
        try{
             $email = $this->emailRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new EmailResource($email),'Email Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $email = $this->emailRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new EmailResource($email),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Email $email)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransaction();
        try{
             $email = $this->emailRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('Email Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->emailRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Email Delete Successful','',204);
    }
}