<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $data->load('company:id,name');
        foreach ($data as $user) {
            $user->role = $user->getRoleNames()->first();
        }

        return ApiResponseClass::sendResponse(UserResource::collection($data), '', 200);
    }

    /**
     * Store a newly created resource in storage.
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

        $role = $request->role; // Retrieve the role from the request

        DB::beginTransaction();
        try {
            // Create the user
            $user = $this->userRepositoryInterface->store($details);

            // Assign the role to the user if present
            if ($role) {
                $user->assignRole($role); // Use Spatie to assign the role
            }

            DB::commit();
            return ApiResponseClass::sendResponse(new UserResource($user), 'User Create Successful', 201);

        } catch (\Exception $ex) {
            DB::rollback();
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = $this->userRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        // Build the update details without including the password initially
        $updateDetails = [
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'company_id' => $request->company_id
        ];

        // Only add the password if it is present
        if ($request->filled('password')) {
            $updateDetails['password'] = bcrypt($request->password); // Encrypt the password
        }

        $role = $request->role; // Retrieve the role from the request

        DB::beginTransaction();
        try {
            // Update the user details
            $this->userRepositoryInterface->update($updateDetails, $id);

            // Get the updated user
            $user = $this->userRepositoryInterface->getById($id);

            // Assign the new role to the user if present
            if ($role) {
                $user->syncRoles([$role]); // Use Spatie to update the roles
            }

            DB::commit();
            return ApiResponseClass::sendResponse('User Update Successful', '', 201);

        } catch (\Exception $ex) {
            DB::rollback();
            return ApiResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->userRepositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('User Delete Successful', '', 204);
    }

    /**
     * Returns the authenticated user attributes as well as their role.
     */
    public function getAuthenticatedUser(Request $request)
    {
        $user = $this->userRepositoryInterface->getAuthenticatedUser($request);
        $user->load('company:id,name');
        $user->role = $user->getRoleNames()->first();

        return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
    }

        /**
     * Retrieve users with a specific role.
     */
    public function getUsersByRole(Request $request)
    {
        $role = $request->input('role');

        // Validate the role parameter
        if (!$role) {
            return ApiResponseClass::sendResponse(null, 'Role parameter is required.', 400);
        }

        try {
            // Fetch users with the specified role
            $users = $this->userRepositoryInterface->index()
                ->filter(fn($user) => $user->hasRole($role));

            // Add role to each user for clarity
            foreach ($users as $user) {
                $user->role = $user->getRoleNames()->first();
            }

            return ApiResponseClass::sendResponse(UserResource::collection($users), '', 200);
        } catch (\Exception $ex) {
            return ApiResponseClass::rollback($ex);
        }
    }
}
