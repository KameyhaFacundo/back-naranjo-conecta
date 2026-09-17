<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta el acceso de las cuentas suspendidas (activo = false) aunque
 * tengan un token válido, y les borra el token para forzar el reingreso.
 * Se usa junto a `auth:sanctum`.
 */
class UsuarioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->activo) {
            $user->currentAccessToken()?->delete();

            abort(403, 'Tu cuenta está suspendida. Contactate con la comuna.');
        }

        return $next($request);
    }
}
