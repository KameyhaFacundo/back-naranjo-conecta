<?php

namespace App\Http\Controllers;

use App\Domain\Avisos\Models\Aviso;
use App\Domain\Comercios\Models\Comercio;
use App\Domain\Empleos\Models\PublicacionEmpleo;
use App\Domain\Instituciones\Models\Institucion;
use App\Domain\Productores\Models\Productor;
use App\Domain\Reclamos\Models\Reclamo;
use App\Domain\Resenas\Models\Resena;
use App\Domain\Servicios\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Resumen general del panel: totales de usuarios, publicaciones y reclamos.
 */
class AdminResumenController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'usuarios' => [
                'total' => User::count(),
                'activos' => User::where('activo', true)->count(),
                'suspendidos' => User::where('activo', false)->count(),
                'nuevos_30_dias' => User::where('created_at', '>=', now()->subDays(30))->count(),
                'por_rol' => User::query()
                    ->selectRaw('rol, count(*) as total')
                    ->groupBy('rol')
                    ->pluck('total', 'rol'),
            ],
            'publicaciones' => [
                'servicios' => Servicio::count(),
                'comercios' => Comercio::count(),
                'productores' => Productor::count(),
                'empleos' => PublicacionEmpleo::count(),
                'instituciones' => Institucion::count(),
                'avisos' => Aviso::count(),
                'resenas' => Resena::count(),
            ],
            'reclamos' => [
                'total' => Reclamo::count(),
                'pendientes' => Reclamo::whereIn('estado', ['pendiente', 'en_revision', 'en_proceso'])->count(),
                'por_estado' => Reclamo::query()
                    ->selectRaw('estado, count(*) as total')
                    ->groupBy('estado')
                    ->pluck('total', 'estado'),
            ],
        ]);
    }
}
