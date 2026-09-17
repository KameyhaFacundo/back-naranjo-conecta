<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso al panel administrativo. Se usa junto a
 * `auth:sanctum`, así que acá el usuario ya está autenticado.
 */
class EsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->esAdmin()) {
            abort(403, 'Solo el panel administrativo puede hacer esto.');
        }

        return $next($request);
    }
}
