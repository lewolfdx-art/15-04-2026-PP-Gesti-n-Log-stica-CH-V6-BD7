<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Operadores (trabajadores) asignados a la obra de cada contrato
        Schema::create('contrato_operador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->timestamp('notificado_at')->nullable();
            $table->timestamps();

            $table->unique(['contrato_id', 'trabajador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_operador');
    }
};
