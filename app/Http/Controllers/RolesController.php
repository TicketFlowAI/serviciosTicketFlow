<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Interfaces\RoleRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="API Endpoints for managing roles"
 * )
 */
class RolesController extends Controller
{
    private RoleRepositoryInterface $roleRepositoryInterface;

    public function __construct(RoleRepositoryInterface $roleRepositoryInterface)
    {
        $this->roleRepositoryInterface = $roleRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     summary="Get a list of roles",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        $data = $this->roleRepositoryInterface->index();

        // Check if the current user is a technician
        if (auth()->user()->hasRole('technician')) {
            $data = $data->filter(function ($role) {
                return $role->name !== 'super-admin';
            });
        }

        return ApiResponseClass::sendResponse(RoleResource::collection($data), '', 200);
    }

    /**
     * @OA\Post(
     *     path="/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "permissions"},
     *             @OA\Property(property="name", type="string", example="Admin"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="view_users")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreRoleRequest $request)
    {
        $details = [
            'name' => $request->name,
            'permissions' => $request->permissions,
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
     * @OA\Get(
     *     path="/roles/{id}",
     *     summary="Get details of a specific role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the role",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(response=404, description="Role not found")
     * )
     */
    public function show($id)
    {
        $role = $this->roleRepositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new RoleResource($role), '', 200);
    }

    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     summary="Update a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the role",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "permissions"},
     *             @OA\Property(property="name", type="string", example="Updated Role"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="edit_users")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
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
     * @OA\Delete(
     *     path="/roles/{id}",
     *     summary="Delete a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the role",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Role deleted successfully"),
     *     @OA\Response(response=404, description="Role not found"),
     *     @OA\Response(response=400, description="Cannot delete this role, there are users assigned to it")
     * )
     */
    public function destroy($id)
    {
        try {
            $this->roleRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('Role Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse($ex->getMessage(), '', 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/permissions",
     *     summary="Get a list of all permissions",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of permissions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PermissionResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No permissions found",
     *         @OA\JsonContent(
     *             type="string",
     *             example="No permissions found"
     *         )
     *     )
     * )
     */
    public function listPermissions()
    {
        $permissions = Permission::all();
    
        if ($permissions->isEmpty()) {
            return ApiResponseClass::sendResponse('No permissions found', 404);
        }
        return ApiResponseClass::sendResponse(PermissionResource::collection($permissions), '', 200);
    }

}

