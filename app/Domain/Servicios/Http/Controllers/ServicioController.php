<?php

namespace App\Domain\Servicios\Http\Controllers;

use App\Domain\Servicios\Http\Requests\ServicioRequest;
use App\Domain\Servicios\Http\Resources\ServicioResource;
use App\Domain\Servicios\Models\Servicio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::query()
            ->with(['categoria', 'user'])
            ->withAvg('resenas as resenas_promedio', 'puntuacion')
            ->withCount('resenas')
            // El panel admin ve también las publicaciones ocultas.
            ->when(! $request->user()?->esAdmin(), fn ($q) => $q->where('activo', true))
            ->when($request->query('categoria_id'), fn ($q, $id) => $q->where('categoria_id', $id))
            ->when($request->query('q'), fn ($q, $texto) => $q->where(function ($q) use ($texto) {
                $q->where('titulo', 'like', "%{$texto}%")
                    ->orWhere('subcategoria', 'like', "%{$texto}%");
            }));

        // "Buscar cerca de mí": ?lat=&lng= ordena por distancia (fórmula de Haversine).
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->query('lat');
            $lng = (float) $request->query('lng');

            $query->selectRaw(
                '*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distancia_km',
                [$lat, $lng, $lat]
            )->whereNotNull('lat')->whereNotNull('lng')->orderBy('distancia_km');
        } else {
            $query->latest();
        }

        return ServicioResource::collection($query->paginate(20));
    }

    public function store(ServicioRequest $request)
    {
        $servicio = $request->user()->servicios()->create($request->validated());

        return new ServicioResource($servicio->load(['categoria', 'user']));
    }

    public function show(Servicio $servicio)
    {
        return new ServicioResource($servicio->load(['categoria', 'user']));
    }

    public function update(ServicioRequest $request, Servicio $servicio)
    {
        Gate::allowIf(fn ($user) => $user->id === $servicio->user_id || $user->esAdmin());

        $data = $request->validated();
        // Ocultar/restaurar una publicación es solo del panel admin.
        if (! $request->user()->esAdmin()) {
            unset($data['activo']);
        }

        $servicio->update($data);

        return new ServicioResource($servicio->load(['categoria', 'user']));
    }

    public function destroy(Request $request, Servicio $servicio)
    {
        Gate::allowIf(fn ($user) => $user->id === $servicio->user_id || $user->esAdmin());

        $servicio->delete();

        return response()->noContent();
    }
}
