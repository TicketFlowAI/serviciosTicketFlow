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
        $data = $this->userRepositoryInterface->index();
        $data->load('company:id,name');
        foreach ($data as $user) {
            $user->role = $user->getRoleNames()->first();
        }

        return ApiResponseClass::sendResponse(UserResource::collection($data), '', 200);
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
            DB::rollback();
            return ApiResponseClass::rollback($ex);
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
        $user = $this->userRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
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
            DB::rollback();
            return ApiResponseClass::rollback($ex);
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
        $this->userRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('User Delete Successful', '', 204);
    }

    /**
     * @OA\Get(
     *     path="/users/authenticated",
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
        $user = $this->userRepositoryInterface->getAuthenticatedUser($request);
        $user->load('company:id,name');
        $user->role = $user->getRoleNames()->first();

        return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
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
            return ApiResponseClass::rollback($ex);
        }
    }
}
