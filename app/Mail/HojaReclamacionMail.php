<?php

namespace App\Mail;

use App\Models\LibroReclamacion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Copia de la hoja de reclamación que se envía al consumidor al registrarla.
 */
class HojaReclamacionMail extends Mailable
{
    public function __construct(public LibroReclamacion $hoja)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Hoja de reclamación {$this->hoja->codigo} - " . config('empresa.razon_social'),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.reclamaciones.hoja');
    }
}
