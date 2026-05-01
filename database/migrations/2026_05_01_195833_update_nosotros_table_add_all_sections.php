<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            // Quiénes Somos
            $table->string('quienes_somos_title')->nullable()->after('id');
            $table->text('quienes_somos_text')->nullable()->after('quienes_somos_title');
            $table->string('quienes_somos_image')->nullable()->after('quienes_somos_text');
            
            // Misión
            $table->string('mision_title')->nullable()->after('quienes_somos_image');
            $table->text('mision_text')->nullable()->after('mision_title');
            $table->string('mision_image')->nullable()->after('mision_text');
            
            // Visión
            $table->string('vision_title')->nullable()->after('mision_image');
            $table->text('vision_text')->nullable()->after('vision_title');
            $table->string('vision_image')->nullable()->after('vision_text');
            
            // Valores
            $table->string('valores_title')->nullable()->after('vision_image');
            $table->text('valores_list')->nullable()->after('valores_title');
            $table->string('valores_image')->nullable()->after('valores_list');
            
            // Actividad Principal
            $table->string('actividad_title')->nullable()->after('valores_image');
            $table->text('actividad_text')->nullable()->after('actividad_title');
            $table->string('actividad_image')->nullable()->after('actividad_text');
            
            // Organigrama
            $table->string('organigrama_title')->nullable()->after('actividad_image');
            $table->text('organigrama_text')->nullable()->after('organigrama_title');
            $table->string('organigrama_image')->nullable()->after('organigrama_text');
        });
    }

    public function down(): void
    {
        Schema::table('nosotros', function (Blueprint $table) {
            $table->dropColumn([
                'quienes_somos_title',
                'quienes_somos_text',
                'quienes_somos_image',
                'mision_title',
                'mision_text',
                'mision_image',
                'vision_title',
                'vision_text',
                'vision_image',
                'valores_title',
                'valores_list',
                'valores_image',
                'actividad_title',
                'actividad_text',
                'actividad_image',
                'organigrama_title',
                'organigrama_text',
                'organigrama_image',
            ]);
        });
    }
};