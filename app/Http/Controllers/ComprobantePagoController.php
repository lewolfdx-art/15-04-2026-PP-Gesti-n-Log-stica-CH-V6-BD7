<?php

namespace App\Http\Controllers;

use App\Models\PagoPersonal;

class ComprobantePagoController extends Controller
{
    /**
     * Comprobante imprimible (Imprimir / Guardar como PDF desde el navegador)
     */
    public function show(PagoPersonal $pago)
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'admin']), 403);

        $pago->load(['trabajador', 'contratos' => fn ($q) => $q->orderBy('fecha'), 'registradoPor']);

        return view('comprobantes.pago-personal', ['pago' => $pago]);
    }
}
