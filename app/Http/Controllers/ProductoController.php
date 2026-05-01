<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return view('productos.index');
    }
    
    public function show($slug)
    {
        $producto = Producto::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('productos.show', compact('producto'));
    }
}