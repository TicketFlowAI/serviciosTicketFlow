<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
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

        $role = $request->role; // Obtener el rol desde el request

        DB::beginTransaction();
        try {
            // Crear el usuario
            $user = $this->userRepositoryInterface->store($details);

            // Asignar el rol al usuario si está presente
            if ($role) {
                $user->assignRole($role); // Utiliza Spatie para asignar el rol
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
        // Construir los detalles de actualización sin incluir la contraseña inicialmente
        $updateDetails = [
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'company_id' => $request->company_id
        ];
    
        // Solo agregar la contraseña si está presente
        if ($request->filled('password')) {
            $updateDetails['password'] = bcrypt($request->password); // Encriptar la contraseña
        }
    
        $role = $request->role; // Obtener el rol desde el request
    
        DB::beginTransaction();
        try {
            // Actualizar los detalles del usuario
            $this->userRepositoryInterface->update($updateDetails, $id);
    
            // Obtener el usuario actualizado
            $user = $this->userRepositoryInterface->getById($id);
    
            // Asignar el nuevo rol al usuario si está presente
            if ($role) {
                $user->syncRoles([$role]); // Utiliza Spatie para actualizar los roles
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
     * Returns the authenticated user attributes aswell as their role.
     */
    public function getAuthenticatedUser(Request $request)
    {
        $user = $this->userRepositoryInterface->getAuthenticatedUser($request);
        $user->load('company:id,name');
        $user->role = $user->getRoleNames()->first();

        return ApiResponseClass::sendResponse(new UserResource($user), '', 200);
    }
}
