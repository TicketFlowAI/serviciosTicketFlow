<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/user/{id}', function (string $id) {
    return new UserResource(UserResource::findOrFail($id));
})->middleware('auth:sanctum');
//Route::group(['middleware' => ['role:manager']], function () { ... });