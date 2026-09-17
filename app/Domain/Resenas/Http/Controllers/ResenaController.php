<?php

namespace App\Domain\Resenas\Http\Controllers;

use App\Domain\Comercios\Models\Comercio;
use App\Domain\Productores\Models\Productor;
use App\Domain\Resenas\Http\Resources\ResenaResource;
use App\Domain\Resenas\Models\Resena;
use App\Domain\Servicios\Models\Servicio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Reseñas (estrellas + comentarios) de los módulos calificables.
 * Cada vecino puede dejar una sola reseña por publicación y editarla.
 */
class ResenaController extends Controller
{
    /** Módulos que se pueden calificar: uri => modelo. */
    private const MODULOS = [
        'comercios' => Comercio::class,
        'servicios' => Servicio::class,
        'productores' => Productor::class,
    ];

    public function index(Request $request, string $modulo, int $id)
    {
        // La ruta es pública, pero si viene un token resolvemos al usuario
        // para marcar cuál es su propia reseña (es_mia).
        $user = $request->user() ?? auth('sanctum')->user();
        $request->setUserResolver(fn () => $user);

        $item = $this->resolverItem($modulo, $id);
        $resenas = $item->resenas()->with('user')->latest()->get();

        return response()->json([
            'data' => ResenaResource::collection($resenas)->resolve($request),
            'promedio' => $resenas->isEmpty() ? null : round((float) $resenas->avg('puntuacion'), 1),
            'total' => $resenas->count(),
        ]);
    }

    public function store(Request $request, string $modulo, int $id)
    {
        $item = $this->resolverItem($modulo, $id);

        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $resena = $item->resenas()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data,
        );
        $resena->load('user');

        return (new ResenaResource($resena))
            ->response()
            ->setStatusCode($resena->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, Resena $resena)
    {
        Gate::allowIf(fn ($user) => $user->id === $resena->user_id || $user->esAdmin());

        $resena->delete();

        return response()->noContent();
    }

    private function resolverItem(string $modulo, int $id)
    {
        abort_unless(isset(self::MODULOS[$modulo]), 404);

        return self::MODULOS[$modulo]::findOrFail($id);
    }
}
