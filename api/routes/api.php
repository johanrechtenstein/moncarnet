<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\AuthController;

//routes public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/check-pseudo', [AuthController::class, 'checkPseudo']);


//routes privés à partir de là

Route::middleware('auth:sanctum')->group(function () {

Route::post('/logout', [AuthController::class, 'logout']);

Route::put('/user', [AuthController::class, 'update']);

Route::delete('/user', [AuthController::class, 'destroy']);

Route::get('/user', function (Request $request) {
    return $request->user();});



// Gestion des Voitures (Remplace 5 lignes GET/POST/PUT/DELETE)
//Route::apiResource('cars', CarController::class);    
//cars
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);
Route::post('/cars', [CarController::class, 'store']);
Route::delete('/cars/{id}', [CarController::class, 'destroy']);
Route::put('/cars/{id}', [CarController::class, 'update']);


//maintenances.
Route::post('/maintenances', [MaintenanceController::class, 'store']);
Route::delete('/maintenances/{id}', [MaintenanceController::class, 'destroy']);
Route::put('/maintenances/{id}', [MaintenanceController::class, 'update']);
});