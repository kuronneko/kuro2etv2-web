<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\File2eController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')
        ->group(function () {
            Route::post('login', [AuthController::class, 'standarLogin']);
            /*         Route::post('oauth/token', [AuthController::class, 'externalLogin']);
        Route::post('verificar-email', [AuthController::class, 'verifyEmail']);
        Route::put('actualizar', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
        Route::put('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::put('verificar-cedula', [AuthController::class, 'verifyIdentity'])->middleware('auth:sanctum');
        Route::delete('eliminar', [AuthController::class, 'destroy'])->middleware('auth:sanctum');
        Route::put('ocultar', [AuthController::class, 'hide'])->middleware('auth:sanctum');
        Route::put('desocultar', [AuthController::class, 'makeVisible'])->middleware('auth:sanctum'); */
        });

    /**
     * Rutas protegidas por Sanctum.
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('file2es')->group(function () {
            Route::get('get-all', [File2eController::class, 'getAll']);
            Route::get('get-by-id/{id}', [File2eController::class, 'getById']);
/*             Route::get('/', [TicketController::class, 'getByUser']);
            Route::post('{id}/solicitar', [TicketController::class, 'postSolicitud']);
            Route::post('{ticket}/rechazar', [TicketController::class, 'declineTicket']);
            Route::post('', [TicketController::class, 'store']);
            Route::get('obtener/feed', s[TicketController::class, 'getFeed']);
            Route::put('{ticket}/solicitudes/{solicitud}/aceptar', [TicketController::class, 'acceptRequest']);
            Route::put('{ticket}/solicitudes/{solicitud}/rechazar', [TicketController::class, 'declineRequest']); */
        });
    });
});
