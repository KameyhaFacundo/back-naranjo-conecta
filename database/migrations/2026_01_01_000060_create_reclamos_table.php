<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // alumbrado | calles | agua | basura | electrico | caminos | espacios_publicos | otro
            $table->string('categoria')->index();
            $table->text('descripcion');
            $table->string('foto_url')->nullable();
            $table->string('zona')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            // pendiente -> en_revision -> en_proceso -> resuelto
            $table->string('estado')->default('pendiente')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};
