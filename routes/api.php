<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CursoController;
use App\Http\Controllers\Api\V1\PeriodoController;
use App\Http\Controllers\Api\V1\ProgramacionController;
use App\Http\Controllers\Api\V1\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Backend conectado correctamente',
        'timestamp' => now()
    ]);
});

// Rutas Públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Rutas de autenticación
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas de Periodos
    Route::prefix('periodos')->group(function () {
        Route::get('/', [PeriodoController::class, 'index']);
        Route::get('/active', [PeriodoController::class, 'active']);
        Route::get('/{id}', [PeriodoController::class, 'show']);
        Route::post('/', [PeriodoController::class, 'store']);
        Route::put('/{id}', [PeriodoController::class, 'update']);
        Route::delete('/{id}', [PeriodoController::class, 'destroy']);
        Route::patch('/{id}/activate', [PeriodoController::class, 'setActive']);
        Route::patch('/{id}/deactivate', [PeriodoController::class, 'deactivate']);
    });

    // Rutas de Programacion Académica
    Route::get('/programacion', [ProgramacionController::class, 'index']);
    Route::get('/programacion/{id}', [ProgramacionController::class, 'show']);
    // SOLO Secretaría y Admin pueden importar el Excel
    Route::post('/programacion/import', [ProgramacionController::class, 'import'])
        ->middleware('role:secretaria|admin');

    // Ruta de cursos
    Route::get('/cursos', [CursoController::class, 'index']);
    Route::get('/cursos/{id}', [CursoController::class, 'show']);

    // Rutas de solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index']);
    Route::post('/solicitudes', [SolicitudController::class, 'store']);
    Route::get('/solicitudes/{id}', [SolicitudController::class, 'show'])
        ->middleware('role:estudiante|admin');
});
