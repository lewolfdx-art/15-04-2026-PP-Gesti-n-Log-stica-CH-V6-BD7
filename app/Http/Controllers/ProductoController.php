<?php

namespace App\Http\Controllers;

use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::active()->paginate(9);
        return view('productos.index', compact('productos'));
    }
    
    public function show($slug)
    {
        $producto = Producto::where('slug', $slug)->active()->firstOrFail();
        return view('productos.show', compact('producto'));
    }
}