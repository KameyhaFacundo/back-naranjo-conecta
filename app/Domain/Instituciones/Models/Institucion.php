<?php

namespace App\Domain\Instituciones\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';

    protected $fillable = [
        'tipo', 'nombre', 'descripcion', 'direccion', 'telefono', 'horarios', 'lat', 'lng',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
        ];
    }
}
