<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            
            // Configuración de la página
            $table->string('page_title')->nullable();
            $table->text('page_description')->nullable();
            
            // Listas de productos
            $table->text('tipos_concreto')->nullable(); // JSON
            $table->text('servicios_complementarios')->nullable(); // JSON
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};