<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServiceContractController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTermController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Route::group(['middleware' => ['role:manager']], function () { ... });


Route::middleware(['auth:sanctum'])->group(function () {
    // Protected with sanctum API routes
    Route::apiResource('/companies',CompanyController::class);
    Route::apiResource('/services',ServiceController::class);
    Route::apiResource('/taxes',TaxController::class);
    Route::apiResource('/categories',CategoryController::class);
    Route::apiResource('/serviceTerms',ServiceTermController::class);
    Route::apiResource('/servicecontracts',ServiceContractController::class);
    Route::apiResource('/tickets',TicketController::class);
    Route::apiResource('/messages',MessageController::class);
});