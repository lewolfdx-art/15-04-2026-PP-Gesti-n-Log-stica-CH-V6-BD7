<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inicio extends Model
{
    protected $table = 'inicios';
    
    protected $fillable = [
        'hero_title', 'hero_description', 'hero_image',
        'beneficios', 'proyectos_recientes', 'nuestras_operaciones', 'cta_text', 
        'cta_url', 'is_active',
        // Logo
        'logo_image', 'logo_text', 'logo_text_color', 'logo_span_color',
        // Footer
        'oficina_titulo', 'oficina_direccion', 'oficina_telefono',
        'oficina_celular', 'oficina_email', 'oficina_maps_url',
        'planta_titulo', 'planta_ubicacion', 'planta_maps_url', 'planta_whatsapp',
        'atencion_titulo', 'atencion_horario_lunes_sabado',
        'atencion_horario_domingo', 'atencion_asesora_comercial',
        'social_networks', 'whatsapp_number'
    ];
    
    protected $casts = [
        'beneficios' => 'array',
        'proyectos_destacados' => 'array',
        'proyectos_recientes' => 'array',
        'nuestras_operaciones' => 'array',
        'social_networks' => 'array',
        'is_active' => 'boolean',
    ];
    
    public static function getActiveContent()
    {
        return self::where('is_active', true)->first();
    }
} 