<?php

use App\Http\Controllers\{
    CompanyController,
    ServiceController,
    TaxController,
    CategoryController,
    ServiceTermController,
    ServiceContractController,
    TicketController,
    MessageController,
    UserController,
    EmailController,
    IntervalController,
    RolesController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request, UserController $userController) {
//     $user = $request->user(); // Get the authenticated user
//     return response()->json($userController->getAuthenticatedUser($user));
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Protected with sanctum API routes
    Route::apiResource('/companies', CompanyController::class);
    Route::apiResource('/services', ServiceController::class);
    Route::apiResource('/taxes', TaxController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/serviceTerms', ServiceTermController::class);
    Route::apiResource('/servicecontracts', ServiceContractController::class);
    Route::get('/servicecontracts/bycompany/{id}', [ServiceContractController::class, 'getContractsByCompany']);
    Route::apiResource('/tickets', TicketController::class);
    Route::apiResource('/messages', MessageController::class);
    Route::apiResource('/users', UserController::class);
    Route::get('/user', [UserController::class, 'getAuthenticatedUser']);
    Route::apiResource('/emails', EmailController::class);
    Route::apiResource('/intervals', IntervalController::class);
    Route::post('/tickets/close/{id}', [TicketController::class, 'closeTicket']);
    Route::post('/tickets/reassign/{id}', [TicketController::class, 'assignTicket']);
    Route::get('/users/byrole/{role}', [UserController::class, 'getUsersByRole']);
    Route::apiResource('/roles', RolesController::class);
    Route::get('/permissions', [RolesController::class, 'listPermissions']);
});