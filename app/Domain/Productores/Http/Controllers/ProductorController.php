<?php

namespace App\Domain\Productores\Http\Controllers;

use App\Domain\Productores\Http\Requests\ProductorRequest;
use App\Domain\Productores\Http\Resources\ProductorResource;
use App\Domain\Productores\Models\Productor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductorController extends Controller
{
    public function index(Request $request)
    {
        $query = Productor::query()
            ->withAvg('resenas as resenas_promedio', 'puntuacion')
            ->withCount('resenas')
            // El panel admin ve también las publicaciones ocultas.
            ->when(! $request->user()?->esAdmin(), fn ($q) => $q->where('activo', true))
            ->when($request->query('categoria_id'), fn ($q, $id) => $q->where('categoria_id', $id))
            ->latest();

        return ProductorResource::collection($query->paginate(20));
    }

    public function store(ProductorRequest $request)
    {
        $productor = $request->user()->productores()->create($request->validated());

        return new ProductorResource($productor);
    }

    public function show(Productor $productor)
    {
        return new ProductorResource($productor);
    }

    public function update(ProductorRequest $request, Productor $productor)
    {
        Gate::allowIf(fn ($user) => $user->id === $productor->user_id || $user->esAdmin());

        $data = $request->validated();
        // Ocultar/restaurar una publicación es solo del panel admin.
        if (! $request->user()->esAdmin()) {
            unset($data['activo']);
        }

        $productor->update($data);

        return new ProductorResource($productor);
    }

    public function destroy(Request $request, Productor $productor)
    {
        Gate::allowIf(fn ($user) => $user->id === $productor->user_id || $user->esAdmin());

        $productor->delete();

        return response()->noContent();
    }
}
