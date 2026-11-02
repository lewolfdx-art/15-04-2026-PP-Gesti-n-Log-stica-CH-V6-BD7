<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Inicio;
use App\Models\Producto;

class InicioController extends Controller
{
    public function index()
    {
        $inicio = Inicio::getActiveContent();

        // Los productos son ítems de la página de Productos (tipos de concreto)
        $tiposConcreto = collect(Producto::where('is_active', true)->first()?->tipos_concreto ?? [])
            ->filter(fn ($item) => filled($item['name'] ?? null))
            ->take(6)
            ->values();

        $estructuras = Contrato::ESTRUCTURAS;

        return view('home', compact('inicio', 'tiposConcreto', 'estructuras'));
    }
}
