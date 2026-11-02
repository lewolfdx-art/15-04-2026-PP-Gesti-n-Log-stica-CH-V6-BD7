@extends('layouts.app')

@section('title', 'Nosotros | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $valores = collect($nosotros?->valores_list ?? [])->filter(fn ($v) => filled($v['title'] ?? null));
    $iconosValores = ['bi-shield-check', 'bi-award', 'bi-lightbulb', 'bi-people', 'bi-heart', 'bi-tree'];
@endphp

@include('web.partials.page-hero', [
    'titulo' => 'Construimos con',
    'resaltado' => 'concreto de confianza',
    'texto' => 'Empresa peruana con base en Huancayo, especializada en concreto premezclado para vivienda, infraestructura e industria.',
    'miga' => 'Nosotros',
    'acciones' => true,
])

@if($nosotros)
    {{-- Quiénes somos y actividad principal --}}
    <section class="cm-section">
        <div class="cm-container">
            @foreach([
                ['quienes_somos', 'Quiénes somos', 'bi-buildings'],
                ['actividad', 'Lo que hacemos', 'bi-truck'],
            ] as $i => [$campo, $etiqueta, $icono])
                @if(filled($nosotros->{$campo . '_text'}))
                    <div class="cm-media {{ $i % 2 ? 'cm-media--flip' : '' }} cm-reveal">
                        <div>
                            <div class="cm-eyebrow">{{ $etiqueta }}</div>
                            <h2 class="cm-h2">{{ $nosotros->{$campo . '_title'} ?: $etiqueta }}</h2>
                            <div class="cm-prose">{!! str($nosotros->{$campo . '_text'})->sanitizeHtml() !!}</div>
                        </div>
                        @if($nosotros->{$campo . '_image'})
                            <div class="cm-media__img"><img src="{{ Storage::url($nosotros->{$campo . '_image'}) }}" alt="{{ $nosotros->{$campo . '_title'} }}" loading="lazy"></div>
                        @else
                            <div class="cm-media__img cm-media__img--empty"><i class="bi {{ $icono }}"></i></div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </section>

    {{-- Misión y visión --}}
    @if(filled($nosotros->mision_text) || filled($nosotros->vision_text))
        <section class="cm-section cm-section--sand">
            <div class="cm-container">
                <div class="cm-head cm-reveal">
                    <div class="cm-eyebrow">Nuestro propósito</div>
                    <h2 class="cm-h2">Hacia dónde <span>vamos</span></h2>
                </div>
                <div class="cm-grid-2">
                    @if(filled($nosotros->mision_text))
                        <div class="cm-card cm-reveal">
                            @if($nosotros->mision_image)
                                <div class="cm-card__img"><img src="{{ Storage::url($nosotros->mision_image) }}" alt="{{ $nosotros->mision_title }}" loading="lazy"></div>
                            @else
                                <div class="cm-card__icon"><i class="bi bi-bullseye"></i></div>
                            @endif
                            <h3>{{ $nosotros->mision_title ?: 'Misión' }}</h3>
                            <div class="cm-prose">{!! str($nosotros->mision_text)->sanitizeHtml() !!}</div>
                        </div>
                    @endif
                    @if(filled($nosotros->vision_text))
                        <div class="cm-card cm-card--dark cm-reveal">
                            @if($nosotros->vision_image)
                                <div class="cm-card__img"><img src="{{ Storage::url($nosotros->vision_image) }}" alt="{{ $nosotros->vision_title }}" loading="lazy"></div>
                            @else
                                <div class="cm-card__icon"><i class="bi bi-eye"></i></div>
                            @endif
                            <h3>{{ $nosotros->vision_title ?: 'Visión' }}</h3>
                            <div class="cm-prose">{!! str($nosotros->vision_text)->sanitizeHtml() !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Valores --}}
    @if($valores->isNotEmpty())
        <section class="cm-section">
            <div class="cm-container">
                <div class="cm-head cm-reveal">
                    <div class="cm-eyebrow">Lo que nos guía</div>
                    <h2 class="cm-h2">{{ $nosotros->valores_title ?: 'Nuestros valores' }}</h2>
                </div>
                <div class="cm-grid-3">
                    @foreach($valores as $i => $valor)
                        <div class="cm-feature cm-reveal">
                            <div class="cm-feature__icon"><i class="bi {{ $iconosValores[$i % count($iconosValores)] }}"></i></div>
                            <h3>{{ $valor['title'] }}</h3>
                            <p>{{ $valor['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Organigrama --}}
    @if(filled($nosotros->organigrama_text) || $nosotros->organigrama_image)
        <section class="cm-section cm-section--sand">
            <div class="cm-container">
                <div class="cm-head cm-reveal">
                    <div class="cm-eyebrow">Organización</div>
                    <h2 class="cm-h2">{{ $nosotros->organigrama_title ?: 'Nuestro organigrama' }}</h2>
                    @if(filled($nosotros->organigrama_text))
                        <div class="cm-prose">{!! str($nosotros->organigrama_text)->sanitizeHtml() !!}</div>
                    @endif
                </div>
                @if($nosotros->organigrama_image)
                    <div class="cm-card cm-reveal" style="text-align: center">
                        <img src="{{ Storage::url($nosotros->organigrama_image) }}" alt="Organigrama" loading="lazy" style="max-width: 100%; margin: 0 auto; display: block">
                    </div>
                @endif
            </div>
        </section>
    @endif
@else
    <section class="cm-section"><div class="cm-container cm-head"><p class="cm-lead">Pronto publicaremos esta sección.</p></div></section>
@endif

@include('web.partials.cta-final')
@endsection
