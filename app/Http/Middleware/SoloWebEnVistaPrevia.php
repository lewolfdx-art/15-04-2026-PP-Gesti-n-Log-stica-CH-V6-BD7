<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vista previa con ngrok: quien entra por una dirección de ngrok solo ve la web pública.
 * El panel (/admin, /operador), su login y Livewire responden 404, como si no existieran.
 */
class SoloWebEnVistaPrevia
{
    /** Dominios de ngrok (y los que se agreguen en VISTA_PREVIA_HOSTS, separados por coma) */
    private const DOMINIOS = ['ngrok-free.app', 'ngrok-free.dev', 'ngrok.app', 'ngrok.dev', 'ngrok.io'];

    /** Rutas que no se muestran en la vista previa */
    private const RUTAS_PRIVADAS = ['admin', 'admin/*', 'operador', 'operador/*', 'livewire*', 'filament*', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $vistaPrevia = $this->esVistaPrevia($request);

        if ($vistaPrevia && $request->is(...self::RUTAS_PRIVADAS)) {
            abort(404);
        }

        // La web oculta el acceso a la intranet cuando se ve por ngrok
        View::share('vistaPrevia', $vistaPrevia);

        return $next($request);
    }

    private function esVistaPrevia(Request $request): bool
    {
        $host = Str::lower($request->getHost());
        $extras = array_filter(array_map('trim', explode(',', (string) env('VISTA_PREVIA_HOSTS', ''))));

        foreach ([...self::DOMINIOS, ...$extras] as $dominio) {
            if ($host === $dominio || str_ends_with($host, '.' . $dominio)) {
                return true;
            }
        }

        return false;
    }
}
