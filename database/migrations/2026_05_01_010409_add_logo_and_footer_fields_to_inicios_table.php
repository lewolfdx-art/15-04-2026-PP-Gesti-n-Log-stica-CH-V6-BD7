<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            // Logo
            $table->string('logo_image')->nullable()->after('hero_image');
            $table->string('logo_text')->nullable()->after('logo_image');
            $table->string('logo_text_color')->default('#ffffff')->after('logo_text');
            $table->string('logo_span_color')->default('#b40000')->after('logo_text_color');
            
            // Footer - Oficina Administrativa
            $table->string('oficina_titulo')->default('Oficina Administrativa')->after('proyectos_destacados');
            $table->string('oficina_direccion')->nullable()->after('oficina_titulo');
            $table->string('oficina_telefono')->nullable()->after('oficina_direccion');
            $table->string('oficina_celular')->nullable()->after('oficina_telefono');
            $table->string('oficina_email')->nullable()->after('oficina_celular');
            $table->string('oficina_maps_url')->nullable()->after('oficina_email');
            
            // Footer - Planta de Producción
            $table->string('planta_titulo')->default('Planta de Producción')->after('oficina_maps_url');
            $table->string('planta_ubicacion')->nullable()->after('planta_titulo');
            $table->string('planta_maps_url')->nullable()->after('planta_ubicacion');
            $table->string('planta_whatsapp')->nullable()->after('planta_maps_url');
            
            // Footer - Atención al Cliente
            $table->string('atencion_titulo')->default('Atención al Cliente')->after('planta_whatsapp');
            $table->string('atencion_horario_lunes_sabado')->nullable()->after('atencion_titulo');
            $table->string('atencion_horario_domingo')->nullable()->after('atencion_horario_lunes_sabado');
            $table->string('atencion_asesora_comercial')->nullable()->after('atencion_horario_domingo');
            
            // Redes Sociales
            $table->json('social_networks')->nullable()->after('atencion_asesora_comercial');
        });
    }

    public function down(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            $table->dropColumn([
                'logo_image', 'logo_text', 'logo_text_color', 'logo_span_color',
                'oficina_titulo', 'oficina_direccion', 'oficina_telefono', 
                'oficina_celular', 'oficina_email', 'oficina_maps_url',
                'planta_titulo', 'planta_ubicacion', 'planta_maps_url', 'planta_whatsapp',
                'atencion_titulo', 'atencion_horario_lunes_sabado', 
                'atencion_horario_domingo', 'atencion_asesora_comercial',
                'social_networks'
            ]);
        });
    }
};