<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\GradoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\DocenteMateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ActividadController;

Route::get('/test', function () {
    return 'API funcionando';
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::middleware(['api', 'auth:api'])->group(function () {
    
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('grados', GradoController::class);
    Route::apiResource('materias', MateriaController::class);
    Route::apiResource('matriculas', MatriculaController::class);
    Route::apiResource('cargas', DocenteMateriaController::class);
    Route::apiResource('notas', NotaController::class);
    Route::apiResource('actividades', ActividadController::class);

});