<?php

namespace App\Domain\Usuarios\Http\Controllers;

use App\Domain\Usuarios\Http\Resources\UserResource;
use App\Domain\Usuarios\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['nullable', Rule::in(Roles::autoregistro())],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'zona' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            ...$data,
            'rol' => $data['rol'] ?? Roles::VECINO,
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();

        // El panel administrativo puede suspender una cuenta: si está
        // inactiva no se le permite entrar aunque la contraseña sea correcta.
        if (! $user->activo) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está suspendida. Contactate con la comuna.',
            ]);
        }

        // Registro de acceso para el panel (último ingreso + IP).
        $user->forceFill([
            'ultimo_acceso_at' => now(),
            'ultima_ip' => $request->ip(),
        ])->save();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * El vecino edita su propio perfil. El email queda afuera a propósito
     * (es el identificador de login) — no se acepta aunque venga en el
     * request, porque solo se toman los campos listados acá.
     */
    public function actualizarPerfil(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'rol' => ['nullable', Rule::in(Roles::autoregistro())],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'zona' => ['nullable', 'string', 'max:255'],
            'password_actual' => ['required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['password_actual'], $user->password)) {
                throw ValidationException::withMessages([
                    'password_actual' => 'La contraseña actual no coincide.',
                ]);
            }

            $user->password = Hash::make($data['password']);
        }

        $user->fill(collect($data)->only(['nombre', 'rol', 'telefono', 'whatsapp', 'zona'])->toArray());
        $user->save();

        return new UserResource($user);
    }
}
