<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->decimal('latitud', 10, 7)->nullable()->after('ubicacion_referencia');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            $table->text('descripcion_lugar')->nullable()->after('longitud');
            $table->json('fotos_lugar')->nullable()->after('descripcion_lugar');
            $table->string('viabilidad', 20)->nullable()->after('fotos_lugar'); // viable, observaciones, no_viable
            $table->text('viabilidad_comentario')->nullable()->after('viabilidad');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud', 'descripcion_lugar', 'fotos_lugar', 'viabilidad', 'viabilidad_comentario']);
        });
    }
};
