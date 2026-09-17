<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Para el panel administrativo: saber cuándo y desde dónde entró.
            $table->timestamp('ultimo_acceso_at')->nullable()->after('activo');
            $table->string('ultima_ip', 45)->nullable()->after('ultimo_acceso_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ultimo_acceso_at', 'ultima_ip']);
        });
    }
};
