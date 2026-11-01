<?php

namespace App\Http\Controllers;

use App\Mail\HojaReclamacionMail;
use App\Models\LibroReclamacion;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class LibroReclamacionController extends Controller
{
    public function index()
    {
        return view('libro-reclamaciones.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'claimant_name'     => 'required|string|max:255',
            'document_type'     => ['required', Rule::in(array_keys(LibroReclamacion::DOCUMENTOS))],
            'document_number'   => 'required|string|max:20',
            'email'             => 'required|email|max:255',
            'phone'             => 'required|string|max:30',
            'address'           => 'required|string|max:500',
            'menor_edad'        => 'boolean',
            'apoderado_nombre'  => 'required_if:menor_edad,1|nullable|string|max:255',
            'bien_tipo'         => ['required', Rule::in(array_keys(LibroReclamacion::BIENES))],
            'monto'             => 'nullable|numeric|min:0|max:9999999',
            'bien_descripcion'  => 'required|string|max:2000',
            'order_number'      => 'nullable|string|max:100',
            'tipo'              => ['required', Rule::in(array_keys(LibroReclamacion::TIPOS))],
            'claim_description' => 'required|string|max:5000',
            'petition'          => 'required|string|max:5000',
            'acepta'            => 'accepted',
        ], [
            'acepta.accepted'              => 'Debes confirmar que los datos son verdaderos.',
            'apoderado_nombre.required_if' => 'Si eres menor de edad, indica el nombre de tu padre, madre o apoderado.',
        ]);

        $hoja = LibroReclamacion::create($validated);

        // La copia al consumidor es obligatoria, pero un fallo de correo no debe perder el reclamo
        try {
            Mail::to($hoja->email)->send(new HojaReclamacionMail($hoja));
        } catch (Throwable $e) {
            Log::error("No se pudo enviar la copia de la hoja {$hoja->codigo}: {$e->getMessage()}");
        }

        NotificationService::notificarNuevaReclamacion($hoja);

        return redirect()
            ->route('reclamaciones.index')
            ->with('reclamo_codigo', $hoja->codigo)
            ->with('reclamo_fecha_limite', $hoja->fecha_limite->format('d/m/Y'));
    }
}
