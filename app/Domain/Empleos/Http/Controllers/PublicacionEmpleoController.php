<?php

namespace App\Domain\Empleos\Http\Controllers;

use App\Domain\Empleos\Http\Requests\PublicacionEmpleoRequest;
use App\Domain\Empleos\Http\Resources\PublicacionEmpleoResource;
use App\Domain\Empleos\Models\PublicacionEmpleo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PublicacionEmpleoController extends Controller
{
    public function index(Request $request)
    {
        $query = PublicacionEmpleo::query()
            // El panel admin ve también las publicaciones ocultas.
            ->when(! $request->user()?->esAdmin(), fn ($q) => $q->where('activo', true))
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->when($request->query('categoria_id'), fn ($q, $id) => $q->where('categoria_id', $id))
            ->latest();

        return PublicacionEmpleoResource::collection($query->paginate(20));
    }

    public function store(PublicacionEmpleoRequest $request)
    {
        $empleo = $request->user()->empleos()->create($request->validated());

        return new PublicacionEmpleoResource($empleo);
    }

    public function show(PublicacionEmpleo $empleo)
    {
        return new PublicacionEmpleoResource($empleo);
    }

    public function update(PublicacionEmpleoRequest $request, PublicacionEmpleo $empleo)
    {
        Gate::allowIf(fn ($user) => $user->id === $empleo->user_id || $user->esAdmin());

        $data = $request->validated();
        // Ocultar/restaurar una publicación es solo del panel admin.
        if (! $request->user()->esAdmin()) {
            unset($data['activo']);
        }

        $empleo->update($data);

        return new PublicacionEmpleoResource($empleo);
    }

    public function destroy(Request $request, PublicacionEmpleo $empleo)
    {
        Gate::allowIf(fn ($user) => $user->id === $empleo->user_id || $user->esAdmin());

        $empleo->delete();

        return response()->noContent();
    }
}
