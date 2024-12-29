<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Interfaces\RoleRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    private RoleRepositoryInterface $roleRepositoryInterface;

    public function __construct(RoleRepositoryInterface $roleRepositoryInterface)
    {
        $this->roleRepositoryInterface = $roleRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->roleRepositoryInterface->index();

        return ApiResponseClass::sendResponse(RoleResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $details = [
            'name' => $request->name,
            'permissions' => $request->permissions, // Assuming permissions are passed as an array
        ];

        DB::beginTransaction();
        try {
            $role = $this->roleRepositoryInterface->store($details);

            DB::commit();
            return ApiResponseClass::sendResponse(new RoleResource($role), 'Role Create Successful', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $role = $this->roleRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new RoleResource($role), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $updateDetails = [
            'name' => $request->name,
            'permissions' => $request->permissions,
        ];

        DB::beginTransaction();
        try {
            $this->roleRepositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse('Role Update Successful', '', 201);

        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->roleRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Role Delete Successful', '', 204);
    }

    /**
     * List all possible permissions available in the application.
     */
    public function listPermissions()
    {
        $permissions = Permission::all();

        return ApiResponseClass::sendResponse($permissions, 'Permissions List Retrieved Successfully', 200);
    }
}