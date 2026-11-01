<?php

namespace App\Mail;

use App\Models\LibroReclamacion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Respuesta de la empresa a una hoja de reclamación.
 */
class RespuestaReclamacionMail extends Mailable
{
    public function __construct(public LibroReclamacion $hoja)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Respuesta a su {$this->hoja->tipo} {$this->hoja->codigo} - " . config('empresa.razon_social'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.reclamaciones.respuesta');
    }
}
