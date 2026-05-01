<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nosotros extends Model
{
    protected $table = 'nosotros';
    
    protected $fillable = [
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
        'is_active'
    ];
    
    protected $casts = [
        'valores_list' => 'array',
        'is_active' => 'boolean',
    ];
    
    public static function getActiveContent()
    {
        return self::where('is_active', true)->first();
    }
}