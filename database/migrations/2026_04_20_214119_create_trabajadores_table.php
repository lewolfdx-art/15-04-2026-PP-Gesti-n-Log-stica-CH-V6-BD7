<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            
            $table->string('tipo_cargo');           // operador_planta, asesor_ventas, etc.
            $table->string('nombre_completo');
            $table->string('dni', 8)->unique()->nullable();
            $table->date('fecha_nacimiento')->nullable();
            
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
            
            // Índices
            $table->index(['tipo_cargo', 'activo']);
            $table->index('orden');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trabajadores');
    }
};