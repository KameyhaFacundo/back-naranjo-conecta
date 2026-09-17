<?php

use App\Domain\Avisos\Http\Controllers\AvisoController;
use App\Domain\Categorias\Http\Controllers\CategoriaController;
use App\Domain\Comercios\Http\Controllers\ComercioController;
use App\Domain\Empleos\Http\Controllers\PublicacionEmpleoController;
use App\Domain\Instituciones\Http\Controllers\InstitucionController;
use App\Domain\Productores\Http\Controllers\ProductorController;
use App\Domain\Reclamos\Http\Controllers\ReclamoController;
use App\Domain\Resenas\Http\Controllers\ResenaController;
use App\Domain\Servicios\Http\Controllers\ServicioController;
use App\Domain\Usuarios\Http\Controllers\AdminUserController;
use App\Domain\Usuarios\Http\Controllers\AuthController;
use App\Http\Controllers\AdminResumenController;
use App\Http\Controllers\MisPublicacionesController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - El Naranjo Conecta
|--------------------------------------------------------------------------
| Cada módulo del MVP tiene su propio controlador dentro de
| app/Domain/<Modulo>/Http/Controllers. Para agregar un módulo nuevo,
| seguí el mismo patrón: Modelo + Migración + Controller + Resource +
| (FormRequest si hace falta) + estas rutas.
*/

// Autenticación (Sanctum, tokens de API)
// throttle:6,1 = 6 intentos por minuto por IP — evita fuerza bruta en el
// login y spam de cuentas en el registro, sin que un vecino normal lo note.
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('/olvide-password', [AuthController::class, 'olvidePassword'])->middleware('throttle:5,1');
    Route::post('/resetear-password', [AuthController::class, 'resetearPassword'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'activo'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);
    });
});

// Categorías (taxonomía compartida por Servicios y Comercios)
Route::apiResource('categorias', CategoriaController::class)
    ->only(['index', 'show'])->middleware([]);
Route::middleware(['auth:sanctum', 'activo'])->group(function () {
    Route::apiResource('categorias', CategoriaController::class)
        ->except(['index', 'show']);
});

// Subida de imágenes: cualquier usuario autenticado puede subir una foto
// y recibir su URL pública (se guarda en foto_url / logo_url).
Route::middleware(['auth:sanctum', 'activo'])->post('uploads', [UploadController::class, 'store']);

// Publicaciones propias del usuario autenticado (para editarlas/borrarlas).
Route::middleware(['auth:sanctum', 'activo'])->get('mis-publicaciones', [MisPublicacionesController::class, 'index']);

// Módulos públicos para consulta, protegidos para publicar/editar/borrar
foreach ([
    'servicios' => ServicioController::class,
    'comercios' => ComercioController::class,
    'productores' => ProductorController::class,
    'empleos' => PublicacionEmpleoController::class,
    'instituciones' => InstitucionController::class,
    'avisos' => AvisoController::class,
] as $uri => $controller) {
    Route::apiResource($uri, $controller)->only(['index', 'show']);

    Route::middleware(['auth:sanctum', 'activo'])->group(function () use ($uri, $controller) {
        Route::apiResource($uri, $controller)->except(['index', 'show']);
    });
}

// Reseñas (estrellas + comentarios) de comercios, servicios y productores.
$modulosCalificables = ['comercios', 'servicios', 'productores'];
Route::get('{modulo}/{id}/resenas', [ResenaController::class, 'index'])
    ->whereIn('modulo', $modulosCalificables);
Route::middleware(['auth:sanctum', 'activo'])->group(function () use ($modulosCalificables) {
    Route::post('{modulo}/{id}/resenas', [ResenaController::class, 'store'])
        ->whereIn('modulo', $modulosCalificables);
    Route::delete('resenas/{resena}', [ResenaController::class, 'destroy']);
});

// Reclamos: cualquier vecino autenticado puede crear y ver los propios;
// el cambio de estado (pendiente → en revisión → en proceso → resuelto)
// queda para el panel administrativo.
Route::middleware(['auth:sanctum', 'activo'])->group(function () {
    Route::apiResource('reclamos', ReclamoController::class)->except(['index', 'show']);
    Route::patch('reclamos/{reclamo}/estado', [ReclamoController::class, 'cambiarEstado']);
});
Route::apiResource('reclamos', ReclamoController::class)->only(['index', 'show']);

// Panel administrativo: reutiliza los index de cada módulo, pero como acá
// el usuario es admin incluyen también las publicaciones ocultas
// (activo = false), así se pueden moderar/restaurar.
Route::middleware(['auth:sanctum', 'activo', 'admin'])->prefix('admin')->group(function () {
    Route::get('resumen', [AdminResumenController::class, 'index']);
    Route::get('servicios', [ServicioController::class, 'index']);
    Route::get('comercios', [ComercioController::class, 'index']);
    Route::get('productores', [ProductorController::class, 'index']);
    Route::get('empleos', [PublicacionEmpleoController::class, 'index']);
    Route::get('avisos', [AvisoController::class, 'index']);
    Route::get('instituciones', [InstitucionController::class, 'index']);
    Route::get('reclamos', [ReclamoController::class, 'index']);

    // Gestión de cuentas: ver, suspender/reactivar, resetear clave y borrar.
    Route::get('usuarios', [AdminUserController::class, 'index']);
    Route::get('usuarios/{usuario}', [AdminUserController::class, 'show']);
    Route::patch('usuarios/{usuario}', [AdminUserController::class, 'update']);
    Route::post('usuarios/{usuario}/password', [AdminUserController::class, 'resetPassword']);
    Route::delete('usuarios/{usuario}', [AdminUserController::class, 'destroy']);
});
