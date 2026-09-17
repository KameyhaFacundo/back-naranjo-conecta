<?php

namespace App\Http\Controllers;

use App\Domain\Comercios\Http\Resources\ComercioResource;
use App\Domain\Empleos\Http\Resources\PublicacionEmpleoResource;
use App\Domain\Productores\Http\Resources\ProductorResource;
use App\Domain\Reclamos\Http\Resources\ReclamoResource;
use App\Domain\Servicios\Http\Resources\ServicioResource;
use Illuminate\Http\Request;

/**
 * Publicaciones del usuario autenticado, agrupadas por módulo, para que
 * cada vecino pueda editar o borrar lo suyo desde "Mis publicaciones".
 */
class MisPublicacionesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'servicios' => ServicioResource::collection(
                $user->servicios()->with('categoria')->latest()->get()
            )->resolve(),
            'comercios' => ComercioResource::collection(
                $user->comercios()->with('categoria')->latest()->get()
            )->resolve(),
            'productores' => ProductorResource::collection(
                $user->productores()->latest()->get()
            )->resolve(),
            'empleos' => PublicacionEmpleoResource::collection(
                $user->empleos()->latest()->get()
            )->resolve(),
            'reclamos' => ReclamoResource::collection(
                $user->reclamos()->latest()->get()
            )->resolve(),
        ]);
    }
}
