<?php

namespace App\Domain\Categorias\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $nombre
 * @property string $slug
 * @property string $modulo servicios|comercios|productores
 * @property string|null $icono
 */
class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'slug', 'modulo', 'icono'];

    protected static function booted(): void
    {
        static::creating(function (Categoria $categoria) {
            $categoria->slug ??= Str::slug($categoria->nombre);
        });
    }

    protected static function newFactory(): \Database\Factories\CategoriaFactory
    {
        return \Database\Factories\CategoriaFactory::new();
    }
}
