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
            $table->string('tipo');
            $table->string('valor');
            $table->boolean('es_persona')->default(false);
    
            $table->string('documento')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dato_operacions');
    }
};