<?php

namespace App\Domain\Categorias\Http\Controllers;

use App\Domain\Categorias\Http\Resources\CategoriaResource;
use App\Domain\Categorias\Models\Categoria;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::query()
            ->when($request->query('modulo'), fn ($q, $modulo) => $q->where('modulo', $modulo))
            ->orderBy('nombre')
            ->get();

        return CategoriaResource::collection($categorias);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'modulo' => ['required', 'in:servicios,comercios,productores'],
            'icono' => ['nullable', 'string', 'max:100'],
        ]);

        $categoria = Categoria::create($data);

        return new CategoriaResource($categoria);
    }

    public function show(Categoria $categoria)
    {
        return new CategoriaResource($categoria);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'modulo' => ['sometimes', 'required', 'in:servicios,comercios,productores'],
            'icono' => ['nullable', 'string', 'max:100'],
        ]);

        $categoria->update($data);

        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return response()->noContent();
    }
}
