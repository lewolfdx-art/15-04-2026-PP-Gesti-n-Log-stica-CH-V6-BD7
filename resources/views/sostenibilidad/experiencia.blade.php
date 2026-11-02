@extends('layouts.app')

@section('title', 'Experiencia y obras realizadas | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $obras = App\Models\Inicio::obrasRealizadas();
    $iconos = ['bi-hospital', 'bi-buildings', 'bi-water', 'bi-house', 'bi-bricks', 'bi-signpost-split'];
@endphp

@include('web.partials.page-hero', [
    'titulo' => 'Obras que llevan',
    'resaltado' => 'nuestro concreto',
    'texto' => 'Vivienda, salud, infraestructura hidráulica y más: proyectos del Valle del Mantaro construidos con Concretera Mantaro.',
    'miga' => 'Experiencia',
    'acciones' => true,
])

<section class="cm-section">
    <div class="cm-container">
        @if($obras->isNotEmpty())
            <div class="cm-head cm-reveal">
                <div class="cm-eyebrow">Proyectos realizados</div>
                <h2 class="cm-h2">{{ $obras->count() }} {{ $obras->count() === 1 ? 'obra' : 'obras' }} <span>que nos respaldan</span></h2>
            </div>
            <div class="cm-gallery">
                @foreach($obras as $i => $obra)
                    <div class="cm-tile cm-reveal">
                        @if(! empty($obra['image']))
                            <img src="{{ Storage::url($obra['image']) }}" alt="{{ $obra['title'] }}" loading="lazy">
                        @else
                            <i class="bi {{ $iconos[$i % count($iconos)] }} cm-tile__icon"></i>
                        @endif
                        <span class="cm-tile__tag">Obra</span>
                        <div class="cm-tile__caption">
                            <h3>{{ $obra['title'] }}</h3>
                            @if(! empty($obra['description']))<p>{{ $obra['description'] }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="cm-head"><p class="cm-lead">Pronto publicaremos nuestras obras.</p></div>
        @endif
    </div>
</section>

@include('web.partials.cta-final', [
    'tituloCta' => '¿Tu obra es la siguiente?',
    'textoCta' => 'Cuéntanos tu proyecto y te enviamos una cotización a la medida.',
])
@endsection
