<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastoCategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'grupo', 'parent_id', 'orden', 'activo'
    ];

    public function parent()
    {
        return $this->belongsTo(GastoCategoria::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GastoCategoria::class, 'parent_id');
    }
}