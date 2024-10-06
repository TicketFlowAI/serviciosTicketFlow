<?php

use App\Http\Controllers\CompanyController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//Route::group(['middleware' => ['role:manager']], function () { ... });


Route::middleware(['auth:sanctum'])->group(function () {
    // Protected Company API routes
    Route::apiResource('/companies',CompanyController::class)->middleware('auth:sanctum');
});