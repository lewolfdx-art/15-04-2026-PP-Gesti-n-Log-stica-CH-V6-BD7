<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nosotros', function (Blueprint $table) {
            $table->id();
            
            // Quiénes Somos
            $table->string('quienes_somos_title')->nullable();
            $table->text('quienes_somos_text')->nullable();
            $table->string('quienes_somos_image')->nullable();
            
            // Misión
            $table->string('mision_title')->nullable();
            $table->text('mision_text')->nullable();
            $table->string('mision_image')->nullable();
            
            // Visión
            $table->string('vision_title')->nullable();
            $table->text('vision_text')->nullable();
            $table->string('vision_image')->nullable();
            
            // Valores (lista + imagen general)
            $table->string('valores_title')->nullable();
            $table->text('valores_list')->nullable(); // JSON
            $table->string('valores_image')->nullable();
            
            // Actividad Principal
            $table->string('actividad_title')->nullable();
            $table->text('actividad_text')->nullable();
            $table->string('actividad_image')->nullable();
            
            // Organigrama
            $table->string('organigrama_title')->nullable();
            $table->text('organigrama_text')->nullable();
            $table->string('organigrama_image')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nosotros');
    }
};