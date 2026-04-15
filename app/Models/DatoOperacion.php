<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoOperacion extends Model
{
    protected $fillable = ['tipo', 'valor', 'descripcion', 'orden', 'activo'];
}
