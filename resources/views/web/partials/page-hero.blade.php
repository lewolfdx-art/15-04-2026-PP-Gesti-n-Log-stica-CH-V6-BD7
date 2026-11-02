{{--
    Cabecera de las páginas internas.
    Parámetros: $titulo, $resaltado (parte en naranja, opcional), $texto (opcional),
    $imagen (ruta en el disco public, opcional), $miga (texto de la miga de pan), $acciones (bool, botones Cotizar/WhatsApp)
--}}
@php
    $config ??= App\Models\Inicio::getActiveContent();
@endphp

<section class="cm-page-hero" @if(! empty($imagen)) style="background-image: url('{{ Storage::url($imagen) }}')" @endif>
    <div class="cm-container">
        <nav class="cm-breadcrumb" aria-label="Estás en">
            <a href="{{ route('home') }}">Inicio</a>
            <span aria-hidden="true">/</span>
            <span>{{ $miga ?? $titulo }}</span>
        </nav>
        <h1>{{ $titulo }} @if(! empty($resaltado))<span>{{ $resaltado }}</span>@endif</h1>
        @if(! empty($texto))
            <p>{{ $texto }}</p>
        @endif
        @if($acciones ?? false)
            <div class="cm-page-hero__actions">
                <a href="{{ route('home') }}#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> Cotiza tu obra</a>
            </div>
        @endif
    </div>
</section>
