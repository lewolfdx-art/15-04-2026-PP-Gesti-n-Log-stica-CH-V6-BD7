<?php

namespace App\Http\Controllers;

use App\Models\Nosotros;

class NosotrosController extends Controller
{
    public function index()
    {
        $nosotros = Nosotros::where('is_active', true)->first();
        return view('nosotros.index', compact('nosotros'));
    }
}