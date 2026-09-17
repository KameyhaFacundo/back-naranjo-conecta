<?php

namespace App\Domain\Avisos\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aviso extends Model
{
    protected $fillable = ['user_id', 'titulo', 'cuerpo', 'foto_url', 'tipo', 'fecha_evento'];

    protected function casts(): array
    {
        return [
            'fecha_evento' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
