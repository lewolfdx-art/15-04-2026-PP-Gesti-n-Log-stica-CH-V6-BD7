{{--
    Cabecera de las páginas internas.
    Parámetros: $titulo, $resaltado (parte en naranja, opcional), $texto (opcional),
    $imagen (ruta en el disco public, opcional), $miga (texto de la miga de pan), $acciones (bool, botones Cotizar/WhatsApp)
--}}
@php
    $config ??= App\Models\Inicio::getActiveContent();
    $enlaceWa = ($acciones ?? false)
        ? App\Models\Inicio::enlaceWhatsapp($config->whatsapp_number ?? null, 'Hola, quiero cotizar concreto premezclado para mi obra.')
        : null;
@endphp

<section class="cm-page-hero" @if(! empty($imagen)) style="background-image: url('{{ Storage::url($imagen) }}')" @endif>
    <div class="cm-container">
        <nav class="cm-breadcrumb" aria-label="Estás en">
            <a href="{{ route('home') }}">Inicio</a>
            <i class="bi bi-chevron-right" style="font-size: 11px"></i>
            <span>{{ $miga ?? $titulo }}</span>
        </nav>
        <h1>{{ $titulo }} @if(! empty($resaltado))<span>{{ $resaltado }}</span>@endif</h1>
        @if(! empty($texto))
            <p>{{ $texto }}</p>
        @endif
        @if($acciones ?? false)
            <div class="cm-page-hero__actions">
                <a href="{{ route('home') }}#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> Cotiza tu obra</a>
                @if($enlaceWa)
                    <a href="{{ $enlaceWa }}" target="_blank" rel="noopener" class="cm-btn cm-btn--ghost"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                @endif
            </div>
        @endif
    </div>
</section>
