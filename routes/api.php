<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/auth/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/v1/auth/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/v1/profile',[App\Http\Controllers\AuthController::class, 'profile']);
Route::post('/v1/consultations',[App\Http\Controllers\ConsulController::class, 'addconsul']);
Route::get('/v1/consultations',[App\Http\Controllers\ConsulController::class, 'getconsul']);
Route::get('/v1/spots',[App\Http\Controllers\ConsulController::class, 'getspot']);
Route::get('/v1/spots/{id}',[App\Http\Controllers\ConsulController::class, 'getspotdetail']);