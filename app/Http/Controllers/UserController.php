<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for managing users"
 * )
 */
class UserController extends Controller
{
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get a list of users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index()
    {
        try {
            $currentUser = auth()->user();
            $data = $this->userRepositoryInterface->index();
            $data->load('company:id,name');

            if ($currentUser->hasRole('technician')) {
                $data = $data->filter(function ($user) {
                    return !$user->hasRole('super-admin');
                });
            }

            foreach ($data as $user) {
                $user->role = $user->getRoleNames()->first();
            }

            return ApiResponseClass::sendResponse(UserResource::collection($data), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve users', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "lastname", "email", "password", "company_id", "role"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="securepassword123"),
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreUserRequest $request)
    {
        $currentUser = auth()->user();
        if ($currentUser->hasRole('technician') && $request->role === 'super-admin') {
            return ApiResponseClass::sendResponse(null, 'Technicians cannot assign the super-admin role.', 403);
        }

        $details = [
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'company_id' => $request->company_id
        ];

        $role = $request->role;

        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->store($details);

            if ($role) {
                $user->assignRole($role);
            }

            DB::commit();
            return ApiResponseClass::sendResponse(new UserResource($user), 'User Create Successful', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to create user', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get details of a specific user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id)
    {
        try {
            $user = $this->userRepositoryInterface->getById($id);
            return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve user', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "lastname", "email", "company_id"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="lastname", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="newsecurepassword123"),
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="role", type="string", example="editor")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $currentUser = auth()->user();
        if ($currentUser->hasRole('technician') && $request->role === 'super-admin') {
            return ApiResponseClass::sendResponse(null, 'Technicians cannot assign the super-admin role.', 403);
        }

        $updateDetails = [
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'company_id' => $request->company_id
        ];

        if ($request->filled('password')) {
            $updateDetails['password'] = bcrypt($request->password);
        }

        $role = $request->role;

        DB::beginTransaction();
        try {
            $this->userRepositoryInterface->update($updateDetails, $id);
            $user = $this->userRepositoryInterface->getById($id);

            if ($role) {
                $user->syncRoles([$role]);
            }

            DB::commit();
            return ApiResponseClass::sendResponse('User Update Successful', '', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to update user', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $this->userRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse('User Delete Successful', '', 204);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to delete user', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Get details of the authenticated user",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user details",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = $this->userRepositoryInterface->getAuthenticatedUser($request);
            $user->load('company:id,name');
            $user->role = $user->getRoleNames()->first();
            $user->twoFactorEnabled = $user->two_factor_confirmed_at ? 1 : 0;

            return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve authenticated user', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/role",
     *     summary="Retrieve users with a specific role",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         required=true,
     *         description="Role of the users to retrieve",
     *         @OA\Schema(type="string", example="admin")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users with the specified role",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Role parameter is required")
     * )
     */
    public function getUsersByRole(Request $request)
    {
        $role = $request->input('role');

        if (!$role) {
            return ApiResponseClass::sendResponse(null, 'Role parameter is required.', 400);
        }

        try {
            $users = $this->userRepositoryInterface->index()
                ->filter(fn($user) => $user->hasRole($role));

            foreach ($users as $user) {
                $user->role = $user->getRoleNames()->first();
            }

            return ApiResponseClass::sendResponse(UserResource::collection($users), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve users by role', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/deleted",
     *     summary="Get a list of deleted users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="List of deleted users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getDeleted()
    {
        try {
            $deletedUsers = $this->userRepositoryInterface->getDeleted();
            return ApiResponseClass::sendResponse(UserResource::collection($deletedUsers), 'Deleted Users Retrieved Successfully', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to retrieve deleted users', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}/restore",
     *     summary="Restore a deleted user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to restore",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User restored successfully"
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $this->userRepositoryInterface->restore($id);
            DB::commit();
            return ApiResponseClass::sendResponse('User Restored Successfully', '', 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Failed to restore user', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}/disable-two-factor",
     *     summary="Disable two-factor authentication for a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Two-factor authentication disabled successfully"
     *     ),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function disableTwoFactorAuthentication($id)
    {
        try {
            $user = $this->userRepositoryInterface->getById($id);
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->two_factor_confirmed_at = null;
            $user->save();

            return ApiResponseClass::sendResponse('Two-factor authentication disabled successfully', '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::sendResponse(null, 'Failed to disable two-factor authentication', 500);
        }
    }
}
