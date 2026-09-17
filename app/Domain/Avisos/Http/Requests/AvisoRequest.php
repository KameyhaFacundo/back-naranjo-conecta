<?php

namespace App\Domain\Avisos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdmin() ?? false;
    }

    public function rules(): array
    {
        $sometimes = $this->isMethod('PUT') || $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'titulo' => [$sometimes, 'string', 'max:255'],
            'cuerpo' => [$sometimes, 'string'],
            'foto_url' => ['nullable', 'url'],
            'tipo' => ['nullable', Rule::in([
                'comunicado', 'reunion', 'evento', 'obra', 'corte_servicio', 'actividad', 'otro',
            ])],
            'fecha_evento' => ['nullable', 'date'],
        ];
    }
}
