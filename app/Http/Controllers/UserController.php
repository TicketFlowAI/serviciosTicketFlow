<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    
    private UserRepositoryInterface $userRepositoryInterface;
    
    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->userRepositoryInterface->index();

        return ApiResponseClass::sendResponse(UserResource::collection($data),'',200);
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
    public function store(StoreUserRequest $request)
    {
        $details =[
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'company_id' => $request->company_id
        ];
        DB::beginTransaction();
        try{
             $user = $this->userRepositoryInterface->store($details);

             DB::commit();
             return ApiResponseClass::sendResponse(new UserResource($user),'User Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = $this->userRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new UserResource($user),'',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $updateDetails =[
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'company_id' => $request->company_id
        ];
        DB::beginTransaction();
        try{
             $user = $this->userRepositoryInterface->update($updateDetails,$id);

             DB::commit();
             return ApiResponseClass::sendResponse('User Update Successful','',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         $this->userRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('User Delete Successful','',204);
    }
}