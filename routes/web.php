<?php

use Illuminate\Support\Facades\Route;

// El frontend (React/PWA) vive en /front y consume la API en /api.
// Esta ruta solo confirma que el backend está corriendo.
Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'status' => 'ok',
        'api' => url('/api'),
    ]);
});
