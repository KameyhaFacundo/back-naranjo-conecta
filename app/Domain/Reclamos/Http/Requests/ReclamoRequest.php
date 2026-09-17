<?php

namespace App\Domain\Reclamos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReclamoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria' => ['required', Rule::in([
                'alumbrado', 'calles', 'agua', 'basura', 'electrico', 'caminos', 'espacios_publicos', 'otro',
            ])],
            'descripcion' => ['required', 'string'],
            'foto_url' => ['nullable', 'url'],
            'zona' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
