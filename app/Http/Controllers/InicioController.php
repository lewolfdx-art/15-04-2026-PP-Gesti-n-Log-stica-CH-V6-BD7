<?php

namespace App\Http\Controllers;

use App\Models\Inicio;
use App\Models\Producto;

class InicioController extends Controller
{
    public function index()
    {
        $inicio = Inicio::getActiveContent();
        $productosDestacados = Producto::active()->limit(6)->get();
        
        return view('home', compact('inicio', 'productosDestacados'));
    }
}