<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1/dewtech')->group(function(){

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api'])->group(function(){
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/validateToken', [AuthController::class, 'validateToken']);
        Route::post('/refreshToken', [AuthController::class, 'refreshToken']);
    });

    Route::prefix('/users')->group(function(){
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

});
