<?php

namespace App\Domain\Comercios\Http\Controllers;

use App\Domain\Comercios\Http\Requests\ComercioRequest;
use App\Domain\Comercios\Http\Resources\ComercioResource;
use App\Domain\Comercios\Models\Comercio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ComercioController extends Controller
{
    public function index(Request $request)
    {
        $query = Comercio::query()
            ->with('categoria')
            ->withAvg('resenas as resenas_promedio', 'puntuacion')
            ->withCount('resenas')
            // El panel admin ve también las publicaciones ocultas.
            ->when(! $request->user()?->esAdmin(), fn ($q) => $q->where('activo', true))
            ->when($request->query('categoria_id'), fn ($q, $id) => $q->where('categoria_id', $id))
            ->when($request->query('q'), fn ($q, $texto) => $q->where('nombre', 'like', "%{$texto}%"))
            ->latest();

        return ComercioResource::collection($query->paginate(20));
    }

    public function store(ComercioRequest $request)
    {
        $comercio = $request->user()->comercios()->create($request->validated());

        return new ComercioResource($comercio->load('categoria'));
    }

    public function show(Comercio $comercio)
    {
        return new ComercioResource($comercio->load('categoria'));
    }

    public function update(ComercioRequest $request, Comercio $comercio)
    {
        Gate::allowIf(fn ($user) => $user->id === $comercio->user_id || $user->esAdmin());

        $data = $request->validated();
        // Ocultar/restaurar una publicación es solo del panel admin.
        if (! $request->user()->esAdmin()) {
            unset($data['activo']);
        }

        $comercio->update($data);

        return new ComercioResource($comercio->load('categoria'));
    }

    public function destroy(Request $request, Comercio $comercio)
    {
        Gate::allowIf(fn ($user) => $user->id === $comercio->user_id || $user->esAdmin());

        $comercio->delete();

        return response()->noContent();
    }
}
