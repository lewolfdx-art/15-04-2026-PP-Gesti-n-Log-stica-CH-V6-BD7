<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Contrato;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Formulario "Cotiza tu obra" de la portada. Se guarda en Mensajes de contacto
 * y avisa a los administradores para que un asesor llame al cliente.
 */
class CotizacionRapidaController extends Controller
{
    public const ASUNTO = 'Solicitud de cotización (web)';

    public function store(Request $request): RedirectResponse
    {
        // Campo trampa: las personas no lo ven, los bots lo llenan
        if (filled($request->input('sitio_web'))) {
            return redirect()->to(route('home') . '#cotizar')->with('cotizacion_enviada', true);
        }

        $datos = $request->validate([
            'nombre'     => ['required', 'string', 'max:120'],
            'telefono'   => ['required', 'string', 'regex:/^[0-9 +\-]{9,15}$/'],
            'lugar'      => ['required', 'string', 'max:160'],
            'volumen'    => ['nullable', 'numeric', 'min:0.5', 'max:2000'],
            'estructura' => ['nullable', Rule::in(array_keys(Contrato::ESTRUCTURAS))],
            'fecha'      => ['nullable', 'date', 'after_or_equal:today'],
            'detalle'    => ['nullable', 'string', 'max:1000'],
        ], [
            'fecha.after_or_equal' => 'La fecha tentativa no puede ser anterior a hoy.',
            'telefono.regex'       => 'Escribe un celular válido (9 dígitos).',
        ], [
            'nombre'   => 'nombre',
            'telefono' => 'celular',
            'lugar'    => 'lugar de la obra',
            'volumen'  => 'volumen',
            'fecha'    => 'fecha tentativa',
        ]);

        $mensaje = collect([
            'Lugar de la obra: ' . $datos['lugar'],
            'Volumen aproximado: ' . (isset($datos['volumen']) ? $datos['volumen'] . ' m³' : 'no indicó'),
            'Estructura: ' . ($datos['estructura'] ?? 'no indicó'),
            'Fecha tentativa: ' . (isset($datos['fecha']) ? \Carbon\Carbon::parse($datos['fecha'])->format('d/m/Y') : 'no indicó'),
            filled($datos['detalle'] ?? null) ? "\nDetalle: " . $datos['detalle'] : null,
        ])->filter()->implode("\n");

        $contacto = Contacto::create([
            'name'    => $datos['nombre'],
            'email'   => null,
            'phone'   => $datos['telefono'],
            'subject' => self::ASUNTO,
            'message' => $mensaje,
        ]);

        NotificationService::notificarSolicitudCotizacionWeb($contacto);

        return redirect()->to(route('home') . '#cotizar')->with('cotizacion_enviada', true);
    }
}
