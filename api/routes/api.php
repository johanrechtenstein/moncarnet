<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;

//route de test api-vue.
Route::get('/test-liaison', function () {
    return response()->json([
        'message' => 'Connexion réussie ! Laravel parle bien à Vue.',
        'status' => 'OK'
    ]);
});

//routes public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/check-email', [AuthController::class, 'checkEmail']);
Route::post('/check-pseudo', [AuthController::class, 'checkPseudo']);
Route::post('/contact', [AuthController::class, 'sendContact'])->middleware('throttle:3,1');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);


//routes privés à partir de là

Route::middleware('auth:sanctum')->group(function () {
    
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
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
// categories
Route::get('/categories', [CategorieController::class, 'index']);
});

// Cette ligne est indispensable pour que Laravel ne crash pas lors de l'envoi du mail
Route::get('/reset-password/{token}', function (Request $request, $token) {
    // On redirige l'utilisateur vers le port 5173 (Vue.js) 
    // en passant le token et l'email dans l'adresse
    return redirect('http://localhost:5173/reset-password/' . $token . '?email=' . $request->email);
})->name('password.reset');
//route de reset de password
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');