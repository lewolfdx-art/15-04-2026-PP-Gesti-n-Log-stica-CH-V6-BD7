<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reportes_semanales', function (Blueprint $table) {
            // Agregar tipo de período
            $table->enum('tipo_periodo', ['semanal', 'quincena', 'fin_mes'])->default('semanal')->after('id');
            
            // Campos para quincena
            $table->enum('quincena', ['PRIMERA', 'SEGUNDA'])->nullable()->after('fecha_hasta');
            $table->integer('mes')->nullable()->after('quincena');
            $table->integer('año')->nullable()->after('mes');
            $table->date('fecha_desde_quincena')->nullable()->after('año');
            $table->date('fecha_hasta_quincena')->nullable()->after('fecha_desde_quincena');
            
            // Campos para fin de mes
            $table->date('fecha_fin_mes')->nullable()->after('fecha_hasta_quincena');
        });
    }

    public function down(): void
    {
        Schema::table('reportes_semanales', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_periodo',
                'quincena',
                'mes',
                'año',
                'fecha_desde_quincena',
                'fecha_hasta_quincena',
                'fecha_fin_mes'
            ]);
        });
    }
};