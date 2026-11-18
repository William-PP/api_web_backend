<?php

use App\Api\Controllers\AuthController;
use App\Api\Controllers\DashboardController;
use App\Api\Controllers\DeportistaController;
use App\Api\Controllers\ModalidadController;
use App\Api\Controllers\PerfilController;
use App\Api\Controllers\PublicDeportistaController;
use App\Api\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/public/deportistas', [PublicDeportistaController::class, 'index']);
Route::get('/public/deportistas/{id}', [PublicDeportistaController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/perfil', [PerfilController::class, 'show']);
    Route::put('/perfil', [PerfilController::class, 'update']);
    Route::post('/perfil/foto', [PerfilController::class, 'updateFoto']);

    Route::apiResource('deportistas', DeportistaController::class);
    Route::apiResource('modalidades', ModalidadController::class);

    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
    Route::get('/dashboard/clima', [DashboardController::class, 'clima']);

    Route::get('/reportes/deportistas.pdf', [ReporteController::class, 'deportistas']);
});
