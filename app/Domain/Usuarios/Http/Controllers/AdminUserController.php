<?php

namespace App\Domain\Usuarios\Http\Controllers;

use App\Domain\Usuarios\Http\Resources\AdminUserResource;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Gestión de cuentas desde el panel administrativo: listar, ver el detalle
 * con sus publicaciones, suspender/reactivar, resetear la contraseña y
 * eliminar. Siempre detrás del middleware `admin`.
 */
class AdminUserController extends Controller
{
    private const RELACIONES = ['servicios', 'comercios', 'productores', 'empleos', 'reclamos', 'resenas'];

    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(self::RELACIONES)
            ->when($request->query('q'), function ($q, $texto) {
                $q->where(function ($q) use ($texto) {
                    $q->where('nombre', 'like', "%{$texto}%")
                        ->orWhere('email', 'like', "%{$texto}%");
                });
            })
            ->when($request->query('rol'), fn ($q, $rol) => $q->where('rol', $rol))
            ->when($request->filled('activo'), fn ($q) => $q->where('activo', $request->boolean('activo')))
            ->latest();

        return AdminUserResource::collection($query->paginate(20));
    }

    public function show(User $usuario)
    {
        $usuario->load(self::RELACIONES)->loadCount(self::RELACIONES);

        return new AdminUserResource($usuario);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        if ($usuario->id === $request->user()->id && ! $data['activo']) {
            abort(422, 'No podés suspender tu propia cuenta.');
        }

        $usuario->update(['activo' => $data['activo']]);

        // Al suspender, se cierran todas sus sesiones activas.
        if (! $data['activo']) {
            $usuario->tokens()->delete();
        }

        return new AdminUserResource($usuario->loadCount(self::RELACIONES));
    }

    public function resetPassword(Request $request, User $usuario)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        // El cast "hashed" del modelo se encarga de encriptarla.
        $usuario->update(['password' => $data['password']]);
        $usuario->tokens()->delete();

        return response()->json(['message' => 'Contraseña actualizada.']);
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            abort(422, 'No podés eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return response()->noContent();
    }
}
