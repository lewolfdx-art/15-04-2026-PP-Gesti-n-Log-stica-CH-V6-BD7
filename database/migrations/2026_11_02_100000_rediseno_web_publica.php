<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rediseño de la web pública:
     * - inicios.cifras y inicios.zonas_cobertura: nuevas secciones editables de la portada
     * - contactos.email opcional: la cotización rápida de la portada solo pide nombre y celular
     */
    public function up(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            $table->json('cifras')->nullable()->after('nuestras_operaciones');
            $table->json('zonas_cobertura')->nullable()->after('cifras');
        });

        Schema::table('contactos', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Cifras tomadas de lo que la web ya decía (Beneficios y Productos); se editan en Página de Inicio
        DB::table('inicios')->whereNull('cifras')->update([
            'cifras' => json_encode([
                ['valor' => '60', 'sufijo' => ' m³/h', 'etiqueta' => 'Capacidad de producción'],
                ['valor' => '15', 'sufijo' => '', 'etiqueta' => 'Mixers en nuestra flota'],
                ['valor' => '6', 'sufijo' => '', 'etiqueta' => 'Tipos de concreto'],
                ['valor' => '400', 'sufijo' => '+ kg/cm²', 'etiqueta' => 'Resistencia máxima'],
            ], JSON_UNESCAPED_UNICODE),
            'zonas_cobertura' => json_encode(['Huancayo', 'El Tambo', 'Chilca', 'Concepción', 'Chupaca', 'Jauja'], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function down(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            $table->dropColumn(['cifras', 'zonas_cobertura']);
        });
    }
};
