<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dato_operacions', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');                    // modo_pago, banco, estado, etc.
            $table->string('valor');
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices para mejor rendimiento
            $table->index(['tipo', 'activo']);
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dato_operacions');
    }
};