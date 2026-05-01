<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'productos';
    
    protected $fillable = [
        'page_title', 'page_description',
        'tipos_concreto', 'servicios_complementarios',
        'is_active'
    ];
    
    protected $casts = [
        'tipos_concreto' => 'array',
        'servicios_complementarios' => 'array',
        'is_active' => 'boolean',
    ];
    
    public static function getActiveContent()
    {
        return self::where('is_active', true)->first();
    }
    
    // 🔧 Agregar este método
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}