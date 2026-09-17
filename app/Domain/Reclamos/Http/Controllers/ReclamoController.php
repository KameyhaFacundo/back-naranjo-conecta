<?php

namespace App\Domain\Reclamos\Http\Controllers;

use App\Domain\Reclamos\Http\Requests\ReclamoRequest;
use App\Domain\Reclamos\Http\Resources\ReclamoResource;
use App\Domain\Reclamos\Models\Reclamo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ReclamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Reclamo::query()
            ->when($request->query('estado'), fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->query('categoria'), fn ($q, $categoria) => $q->where('categoria', $categoria))
            ->latest();

        return ReclamoResource::collection($query->paginate(20));
    }

    public function store(ReclamoRequest $request)
    {
        $reclamo = $request->user()->reclamos()->create([
            ...$request->validated(),
            'estado' => 'pendiente',
        ]);

        return new ReclamoResource($reclamo);
    }

    public function show(Reclamo $reclamo)
    {
        return new ReclamoResource($reclamo);
    }

    public function update(ReclamoRequest $request, Reclamo $reclamo)
    {
        Gate::allowIf(fn ($user) => $user->id === $reclamo->user_id || $user->esAdmin());

        $reclamo->update($request->validated());

        return new ReclamoResource($reclamo);
    }

    public function destroy(Request $request, Reclamo $reclamo)
    {
        Gate::allowIf(fn ($user) => $user->id === $reclamo->user_id || $user->esAdmin());

        $reclamo->delete();

        return response()->noContent();
    }

    /**
     * Cambiar estado (pendiente -> en_revision -> en_proceso -> resuelto).
     * Pensado para el panel administrativo.
     */
    public function cambiarEstado(Request $request, Reclamo $reclamo)
    {
        Gate::allowIf(fn ($user) => $user->esAdmin());

        $data = $request->validate([
            'estado' => ['required', Rule::in(Reclamo::ESTADOS)],
        ]);

        $reclamo->update($data);

        return new ReclamoResource($reclamo);
    }
}
