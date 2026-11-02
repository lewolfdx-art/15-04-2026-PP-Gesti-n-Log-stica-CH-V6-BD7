<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // create_nosotros_table ya trae estas columnas: solo se agregan las que falten
        // (sin esto, migrar una base nueva falla con "columna duplicada")
        Schema::table('nosotros', function (Blueprint $table) {
            $agregar = fn (string $columna) => ! Schema::hasColumn('nosotros', $columna);

            // Quiénes Somos
            if ($agregar('quienes_somos_title')) { $table->string('quienes_somos_title')->nullable()->after('id'); }
            if ($agregar('quienes_somos_text')) { $table->text('quienes_somos_text')->nullable()->after('quienes_somos_title'); }
            if ($agregar('quienes_somos_image')) { $table->string('quienes_somos_image')->nullable()->after('quienes_somos_text'); }
            
            // Misión
            if ($agregar('mision_title')) { $table->string('mision_title')->nullable()->after('quienes_somos_image'); }
            if ($agregar('mision_text')) { $table->text('mision_text')->nullable()->after('mision_title'); }
            if ($agregar('mision_image')) { $table->string('mision_image')->nullable()->after('mision_text'); }
            
            // Visión
            if ($agregar('vision_title')) { $table->string('vision_title')->nullable()->after('mision_image'); }
            if ($agregar('vision_text')) { $table->text('vision_text')->nullable()->after('vision_title'); }
            if ($agregar('vision_image')) { $table->string('vision_image')->nullable()->after('vision_text'); }
            
            // Valores
            if ($agregar('valores_title')) { $table->string('valores_title')->nullable()->after('vision_image'); }
            if ($agregar('valores_list')) { $table->text('valores_list')->nullable()->after('valores_title'); }
            if ($agregar('valores_image')) { $table->string('valores_image')->nullable()->after('valores_list'); }
            
            // Actividad Principal
            if ($agregar('actividad_title')) { $table->string('actividad_title')->nullable()->after('valores_image'); }
            if ($agregar('actividad_text')) { $table->text('actividad_text')->nullable()->after('actividad_title'); }
            if ($agregar('actividad_image')) { $table->string('actividad_image')->nullable()->after('actividad_text'); }
            
            // Organigrama
            if ($agregar('organigrama_title')) { $table->string('organigrama_title')->nullable()->after('actividad_image'); }
            if ($agregar('organigrama_text')) { $table->text('organigrama_text')->nullable()->after('organigrama_title'); }
            if ($agregar('organigrama_image')) { $table->string('organigrama_image')->nullable()->after('organigrama_text'); }
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