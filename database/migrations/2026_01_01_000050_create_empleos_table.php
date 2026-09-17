<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Una sola tabla para "Busco trabajo" y "Busco trabajador" (campo tipo),
        // porque comparten casi todos los campos del doc del proyecto.
        Schema::create('empleos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tipo')->index(); // busco_trabajo | busco_trabajador
            $table->string('titulo');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->text('descripcion')->nullable();
            $table->string('experiencia')->nullable();
            $table->text('habilidades')->nullable();
            $table->text('requisitos')->nullable();
            $table->string('disponibilidad')->nullable();
            $table->string('horario')->nullable();
            $table->string('zona')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleos');
    }
};
