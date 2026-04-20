<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();

            $table->date('fecha')->index();
            
            // Relación con la tabla gasto_categorias (que ya tienes)
            $table->foreignId('gasto_categoria_id')
                  ->constrained('gasto_categorias')
                  ->onDelete('restrict');

            $table->string('subcategoria')->nullable();
            $table->string('responsable')->nullable();
            $table->text('detalle');
            $table->decimal('importe', 12, 2)->default(0);
            $table->string('obs')->nullable();

            // Campos útiles para Filament y auditoría
            $table->timestamps();
            $table->softDeletes(); // opcional, pero recomendado

            // Índices para mejorar velocidad de consultas
            $table->index(['fecha', 'gasto_categoria_id']);
            $table->index('responsable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};