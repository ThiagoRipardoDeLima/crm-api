<?php

use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1/user')->group(function(){
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});
