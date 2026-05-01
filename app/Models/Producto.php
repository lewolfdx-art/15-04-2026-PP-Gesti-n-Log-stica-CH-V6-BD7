<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'productos';
    
    protected $fillable = [
        'name', 'slug', 'description', 'technical_specs',
        'image', 'price', 'stock', 'category', 'is_active'
    ];
    
    protected $casts = [
        'technical_specs' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}