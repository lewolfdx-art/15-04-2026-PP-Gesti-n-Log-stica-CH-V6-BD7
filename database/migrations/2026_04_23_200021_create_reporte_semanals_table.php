<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_semanales', function (Blueprint $table) {
            $table->id();
            
            // Tipo de período
            $table->enum('tipo_periodo', ['semanal', 'quincena', 'fin_mes'])->default('semanal');
            
            // Campos para semanal
            $table->integer('semana')->nullable();
            $table->date('fecha_desde')->nullable();
            $table->date('fecha_hasta')->nullable();
            
            // Campos para quincena
            $table->enum('quincena', ['PRIMERA', 'SEGUNDA'])->nullable();
            $table->integer('mes')->nullable();
            $table->integer('año')->nullable();
            $table->date('fecha_desde_quincena')->nullable();
            $table->date('fecha_hasta_quincena')->nullable();
            
            // Campos para fin de mes
            $table->date('fecha_fin_mes')->nullable();
            
            // Campos comunes
            $table->string('proveedor');
            $table->string('detalle');
            $table->decimal('monto', 12, 2);
            $table->enum('estado', ['CANCELADO', 'DEBE', 'ADELANTO'])->default('DEBE');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_semanales');
    }
};