<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gasto_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');                    // Nombre de la categoría
            $table->string('grupo');                     // Ej: MATERIA PRIMA, GASTOS FIJOS, etc.
            $table->foreignId('parent_id')->nullable()->constrained('gasto_categorias');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gasto_categorias');
    }
};