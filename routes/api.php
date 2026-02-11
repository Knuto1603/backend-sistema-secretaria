<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CursoController;
use App\Http\Controllers\Api\V1\PeriodoController;
use App\Http\Controllers\Api\V1\ProgramacionController;
use App\Http\Controllers\Api\V1\SolicitudController;
use App\Http\Controllers\Api\V1\TipoSolicitudController;
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
    Route::post('/programacion/import', [ProgramacionController::class, 'import'])
        ->middleware('role:secretaria|admin');

    // Ruta de cursos
    Route::get('/cursos', [CursoController::class, 'index']);
    Route::get('/cursos/{id}', [CursoController::class, 'show']);

    // Rutas de Tipos de Solicitud (solo admin/secretaria)
    Route::prefix('tipos-solicitud')->middleware('role:admin|secretaria|decano|secretario academico')->group(function () {
        Route::get('/', [TipoSolicitudController::class, 'index']);
        Route::get('/{id}', [TipoSolicitudController::class, 'show']);
        Route::post('/', [TipoSolicitudController::class, 'store']);
        Route::put('/{id}', [TipoSolicitudController::class, 'update']);
        Route::delete('/{id}', [TipoSolicitudController::class, 'destroy']);
        Route::patch('/{id}/toggle', [TipoSolicitudController::class, 'toggle']);
    });

    // Rutas de Solicitudes
    Route::prefix('solicitudes')->group(function () {
        // Para estudiantes - ver sus propias solicitudes
        Route::get('/mis-solicitudes', [SolicitudController::class, 'misSolicitudes']);

        // Crear solicitud (estudiantes)
        Route::post('/', [SolicitudController::class, 'store']);

        // Para admin/secretaria/decano - ver todas las solicitudes
        Route::get('/', [SolicitudController::class, 'index'])
            ->middleware('role:admin|secretaria|decano|secretario academico');

        // Ver detalle (todos pueden, pero estudiantes solo las suyas)
        Route::get('/{id}', [SolicitudController::class, 'show']);

        // Actualizar estado (admin/secretaria/decano)
        Route::patch('/{id}/estado', [SolicitudController::class, 'updateEstado'])
            ->middleware('role:admin|secretaria|decano|secretario academico');
    });
});
