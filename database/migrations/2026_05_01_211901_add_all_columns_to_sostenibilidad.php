<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sostenibilidad', function (Blueprint $table) {
            // Hero
            $table->string('hero_title')->nullable()->after('id');
            $table->string('hero_image')->nullable()->after('hero_title');
            $table->text('hero_description')->nullable()->after('hero_image');
            
            // Política Ambiental
            $table->string('politica_ambiental_title')->nullable()->after('hero_description');
            $table->string('politica_ambiental_image')->nullable()->after('politica_ambiental_title');
            $table->text('politica_ambiental_list')->nullable()->after('politica_ambiental_image');
            
            // Seguridad y Salud
            $table->string('seguridad_salud_title')->nullable()->after('politica_ambiental_list');
            $table->string('seguridad_salud_image')->nullable()->after('seguridad_salud_title');
            $table->text('seguridad_salud_list')->nullable()->after('seguridad_salud_image');
            
            // Responsabilidad Social
            $table->string('responsabilidad_social_title')->nullable()->after('seguridad_salud_list');
            $table->string('responsabilidad_social_image')->nullable()->after('responsabilidad_social_title');
            $table->text('responsabilidad_social_list')->nullable()->after('responsabilidad_social_image');
            
            // Seguridad en el Trabajo
            $table->text('epp_list')->nullable()->after('responsabilidad_social_list');
            $table->text('protocolos_list')->nullable()->after('epp_list');
            
            // Submódulos
            $table->text('submodulos_list')->nullable()->after('protocolos_list');
            
            // Recursos Académicos
            $table->text('recursos_academicos')->nullable()->after('submodulos_list');
            
            // Proyectos
            $table->text('proyectos_realizados')->nullable()->after('recursos_academicos');
            
            // Recomendaciones
            $table->text('recomendaciones_tecnicas')->nullable()->after('proyectos_realizados');
        });
    }

    public function down(): void
    {
        Schema::table('sostenibilidad', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title', 'hero_image', 'hero_description',
                'politica_ambiental_title', 'politica_ambiental_image', 'politica_ambiental_list',
                'seguridad_salud_title', 'seguridad_salud_image', 'seguridad_salud_list',
                'responsabilidad_social_title', 'responsabilidad_social_image', 'responsabilidad_social_list',
                'epp_list', 'protocolos_list', 'submodulos_list',
                'recursos_academicos', 'proyectos_realizados', 'recomendaciones_tecnicas'
            ]);
        });
    }
};