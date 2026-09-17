<?php

namespace App\Domain\Avisos\Http\Controllers;

use App\Domain\Avisos\Http\Requests\AvisoRequest;
use App\Domain\Avisos\Http\Resources\AvisoResource;
use App\Domain\Avisos\Models\Aviso;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AvisoController extends Controller
{
    // Comunicados/eventos: los publica el panel administrativo,
    // se muestran a todos los vecinos (con lugar para notificaciones push a futuro).

    public function index(Request $request)
    {
        $query = Aviso::query()
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->latest();

        return AvisoResource::collection($query->paginate(20));
    }

    public function store(AvisoRequest $request)
    {
        $aviso = $request->user()->avisos()->create($request->validated());

        return new AvisoResource($aviso);
    }

    public function show(Aviso $aviso)
    {
        return new AvisoResource($aviso);
    }

    public function update(AvisoRequest $request, Aviso $aviso)
    {
        $aviso->update($request->validated());

        return new AvisoResource($aviso);
    }

    public function destroy(Request $request, Aviso $aviso)
    {
        Gate::allowIf(fn ($user) => $user->esAdmin());

        $aviso->delete();

        return response()->noContent();
    }
}
