<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Subida de imágenes (fotos de servicios/reclamos, logos de comercios).
 * Devuelve la URL pública del archivo para guardarla en el campo
 * `foto_url` / `logo_url` del módulo correspondiente.
 */
class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $disco = config('filesystems.uploads');
        $ruta = $request->file('imagen')->store('uploads', $disco);

        return response()->json([
            'url' => Storage::disk($disco)->url($ruta),
        ], 201);
    }
}
