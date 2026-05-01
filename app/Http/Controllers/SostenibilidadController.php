<?php

namespace App\Http\Controllers;

use App\Models\Sostenibilidad;

class SostenibilidadController extends Controller
{
    public function index()
    {
        $sostenibilidad = Sostenibilidad::where('is_active', true)->first();
        return view('sostenibilidad.index', compact('sostenibilidad'));
    }
}