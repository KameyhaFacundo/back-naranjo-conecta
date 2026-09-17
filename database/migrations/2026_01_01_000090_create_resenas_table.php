<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('resenable');
            $table->unsignedTinyInteger('puntuacion');
            $table->text('comentario')->nullable();
            $table->timestamps();

            // Un vecino deja una sola reseña por publicación (puede editarla).
            $table->unique(['user_id', 'resenable_type', 'resenable_id'], 'resenas_usuario_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
