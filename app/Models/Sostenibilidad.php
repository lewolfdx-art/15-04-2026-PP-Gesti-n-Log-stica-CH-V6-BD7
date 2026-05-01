<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sostenibilidad extends Model
{
    protected $table = 'sostenibilidad';
    
    protected $fillable = [
        'hero_title', 'hero_image', 'hero_description',
        'politica_ambiental_title', 'politica_ambiental_image', 'politica_ambiental_list',
        'seguridad_salud_title', 'seguridad_salud_image', 'seguridad_salud_list',
        'responsabilidad_social_title', 'responsabilidad_social_image', 'responsabilidad_social_list',
        'epp_list', 'protocolos_list', 'submodulos_list',
        'recursos_academicos', 'proyectos_realizados', 'recomendaciones_tecnicas',
        'is_active'
    ];
    
    protected $casts = [
        'politica_ambiental_list' => 'array',
        'seguridad_salud_list' => 'array',
        'responsabilidad_social_list' => 'array',
        'epp_list' => 'array',
        'protocolos_list' => 'array',
        'submodulos_list' => 'array',
        'recursos_academicos' => 'array',
        'proyectos_realizados' => 'array',
        'recomendaciones_tecnicas' => 'array',
        'is_active' => 'boolean',
    ];
    
    public static function getActiveContent()
    {
        return self::where('is_active', true)->first();
    }
}