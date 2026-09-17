<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Subida de imágenes (fotos de servicios/reclamos, logos de comercios).
 * Devuelve la URL pública del archivo para guardarla en el campo
 * `foto_url` / `logo_url` del módulo correspondiente.
 */
class UploadController extends Controller
{
    // Lado más largo ya redimensionado: de sobra para verse bien en el
    // celular. Las fotos de cámara vienen muchas veces con 3000-4000px+
    // de lado y varios MB sin necesidad — en 4G se nota.
    private const LADO_MAXIMO = 1600;

    private const CALIDAD_JPEG = 82;

    public function store(Request $request)
    {
        $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $archivo = $request->file('imagen');
        [$contenido, $extension] = $this->comprimir($archivo);

        $disco = config('filesystems.uploads');
        $ruta = 'uploads/'.Str::uuid().'.'.$extension;
        Storage::disk($disco)->put($ruta, $contenido);

        return response()->json([
            'url' => Storage::disk($disco)->url($ruta),
        ], 201);
    }

    /**
     * Redimensiona (si hace falta) y recomprime la imagen antes de
     * guardarla. Si el formato no se puede procesar con GD por algún
     * motivo, se guarda tal cual llegó en vez de fallar la publicación.
     *
     * @return array{0: string, 1: string} contenido binario + extensión
     */
    private function comprimir($archivo): array
    {
        $ruta = $archivo->getRealPath();
        $mime = $archivo->getMimeType();

        $origen = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($ruta),
            'image/png' => @imagecreatefrompng($ruta),
            'image/webp' => @imagecreatefromwebp($ruta),
            default => null,
        };

        if (! $origen) {
            return [file_get_contents($ruta), $this->extensionPara($mime)];
        }

        $anchoOriginal = imagesx($origen);
        $altoOriginal = imagesy($origen);
        $ladoMayor = max($anchoOriginal, $altoOriginal);

        if ($ladoMayor > self::LADO_MAXIMO) {
            $escala = self::LADO_MAXIMO / $ladoMayor;
            $ancho = max(1, (int) round($anchoOriginal * $escala));
            $alto = max(1, (int) round($altoOriginal * $escala));

            $destino = imagecreatetruecolor($ancho, $alto);

            // Preserva transparencia en PNG (logos con fondo transparente).
            if ($mime === 'image/png') {
                imagealphablending($destino, false);
                imagesavealpha($destino, true);
            }

            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $ancho, $alto, $anchoOriginal, $altoOriginal);
            imagedestroy($origen);
            $origen = $destino;
        }

        ob_start();
        match ($mime) {
            'image/png' => imagepng($origen, null, 6),
            'image/webp' => imagewebp($origen, null, self::CALIDAD_JPEG),
            default => imagejpeg($origen, null, self::CALIDAD_JPEG),
        };
        $contenido = ob_get_clean();
        imagedestroy($origen);

        return [$contenido, $this->extensionPara($mime)];
    }

    private function extensionPara(?string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }
}
