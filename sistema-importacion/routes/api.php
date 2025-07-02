<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\ProductoImportadoController;
use App\Http\Controllers\Api\OrdenImportacionController;
use App\Http\Controllers\Api\ContenedorController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas para autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Proveedores
    Route::apiResource('proveedores', ProveedorController::class);
    
    // Productos Importados
    Route::apiResource('productos-importados', ProductoImportadoController::class);
    
    // Órdenes de Importación
    Route::apiResource('ordenes-importacion', OrdenImportacionController::class);
    Route::put('ordenes-importacion/{id}/estado', [OrdenImportacionController::class, 'updateEstado']);
    
    // Contenedores
    Route::apiResource('contenedores', ContenedorController::class);
    Route::get('contenedores/{numeroContenedor}/estado', [ContenedorController::class, 'consultarEstado']);
});

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});