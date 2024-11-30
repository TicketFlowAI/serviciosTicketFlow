<?php

use App\Http\Controllers as Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request, UserController $userController) {
//     $user = $request->user(); // Get the authenticated user
//     return response()->json($userController->getAuthenticatedUser($user));
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Protected with sanctum API routes
    Route::apiResource('/companies',Controllers\CompanyController::class);
    Route::apiResource('/services',Controllers\ServiceController::class);
    Route::apiResource('/taxes',Controllers\TaxController::class);
    Route::apiResource('/categories',Controllers\CategoryController::class);
    Route::apiResource('/serviceTerms',Controllers\ServiceTermController::class);
    Route::apiResource('/servicecontracts',Controllers\ServiceContractController::class);
    Route::get('/servicecontracts/bycompany/{id}', [Controllers\ServiceContractController::class, 'getContractsByCompany']);
    Route::apiResource('/tickets',Controllers\TicketController::class);
    Route::apiResource('/messages',Controllers\MessageController::class);
    Route::apiResource('/users',Controllers\UserController::class);
    Route::get('/user', [Controllers\UserController::class, 'getAuthenticatedUser']);
    Route::apiResource('/emails',Controllers\EmailController::class);
});
