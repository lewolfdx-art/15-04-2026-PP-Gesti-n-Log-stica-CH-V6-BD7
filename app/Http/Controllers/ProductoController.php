<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index()
    {
        return view('productos.index');
    }

    /**
     * Enlace directo a un producto o servicio: /productos/lanzado lleva a su tarjeta
     * dentro de /productos (los productos son ítems de la página, no tienen página propia).
     */
    public function show(string $slug)
    {
        $pagina = Producto::where('is_active', true)->first();

        $existe = collect($pagina?->tipos_concreto ?? [])
            ->merge($pagina?->servicios_complementarios ?? [])
            ->contains(fn ($item) => Str::slug($item['name'] ?? '') === $slug);

        abort_unless($existe, 404);

        return redirect()->to(route('productos.index') . '#' . $slug, 301);
    }
}
