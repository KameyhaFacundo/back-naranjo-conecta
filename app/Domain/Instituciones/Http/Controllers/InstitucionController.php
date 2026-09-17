<?php

namespace App\Domain\Instituciones\Http\Controllers;

use App\Domain\Instituciones\Http\Requests\InstitucionRequest;
use App\Domain\Instituciones\Http\Resources\InstitucionResource;
use App\Domain\Instituciones\Models\Institucion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InstitucionController extends Controller
{
    // El directorio de instituciones lo administra el panel admin
    // (no tiene "dueño" como los demás módulos).

    public function index(Request $request)
    {
        $query = Institucion::query()
            ->when($request->query('tipo'), fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->orderBy('nombre');

        return InstitucionResource::collection($query->paginate(20));
    }

    public function store(InstitucionRequest $request)
    {
        return new InstitucionResource(Institucion::create($request->validated()));
    }

    public function show(Institucion $institucion)
    {
        return new InstitucionResource($institucion);
    }

    public function update(InstitucionRequest $request, Institucion $institucion)
    {
        $institucion->update($request->validated());

        return new InstitucionResource($institucion);
    }

    public function destroy(Request $request, Institucion $institucion)
    {
        Gate::allowIf(fn ($user) => $user->esAdmin());

        $institucion->delete();

        return response()->noContent();
    }
}
