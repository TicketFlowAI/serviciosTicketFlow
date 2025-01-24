<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
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
        try {
            $data = $this->roleRepositoryInterface->index();

            // Check if the current user is a technician
            if (auth()->user()->hasRole('technician')) {
                $data = $data->filter(function ($role) {
                    return $role->name !== 'super-admin';
                });
            }

            return ApiResponseClass::sendResponse(RoleResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve roles', 500);
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
        try {
            $role = $this->roleRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(new RoleResource($role), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve role', 500);
        }
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
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update role', 500);
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
        try {
            $permissions = Permission::all();

            if ($permissions->isEmpty()) {
                return ApiResponseClass::sendResponse('No permissions found', 404);
            }
            return ApiResponseClass::sendResponse(PermissionResource::collection($permissions), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve permissions', 500);
        }
    }

}
