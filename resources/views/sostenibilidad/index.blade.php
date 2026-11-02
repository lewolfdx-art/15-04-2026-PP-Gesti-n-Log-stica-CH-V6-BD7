@extends('layouts.app')

@section('title', 'Sostenibilidad | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $s = $sostenibilidad;
    $lista = fn ($valor) => collect($valor ?? [])->map(fn ($i) => is_array($i) ? ($i['item'] ?? null) : $i)->filter()->values();
    $politicas = collect([
        ['politica_ambiental', 'Política ambiental', 'bi-tree'],
        ['seguridad_salud', 'Seguridad y salud ocupacional', 'bi-shield-check'],
        ['responsabilidad_social', 'Responsabilidad social', 'bi-people'],
    ])->map(fn ($p) => [
        'titulo' => $s?->{$p[0] . '_title'} ?: $p[1],
        'imagen' => $s?->{$p[0] . '_image'},
        'icono'  => $p[2],
        'items'  => $lista($s?->{$p[0] . '_list'}),
    ])->filter(fn ($p) => $p['items']->isNotEmpty());
    $epp = $lista($s?->epp_list);
    $protocolos = $lista($s?->protocolos_list);
    $submodulos = $lista($s?->submodulos_list);
    $recursos = collect($s?->recursos_academicos ?? [])->filter(fn ($r) => filled($r['title'] ?? null));
@endphp

@include('web.partials.page-hero', [
    'titulo' => 'Compromiso con la',
    'resaltado' => 'sostenibilidad',
    'texto' => strip_tags((string) ($s?->hero_description ?? '')) ?: 'Producimos concreto cuidando el ambiente, la seguridad de nuestro equipo y a las comunidades donde trabajamos.',
    'imagen' => $s?->hero_image,
    'miga' => 'Sostenibilidad',
])

{{-- Políticas --}}
@if($politicas->isNotEmpty())
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-head cm-reveal">
                <div class="cm-eyebrow">Nuestras políticas</div>
                <h2 class="cm-h2">Cómo trabajamos <span>de forma responsable</span></h2>
            </div>
            <div class="cm-grid-3">
                @foreach($politicas as $i => $politica)
                    <div class="cm-card {{ $i === 1 ? 'cm-card--dark' : '' }} cm-reveal">
                        @if($politica['imagen'])
                            <div class="cm-card__img"><img src="{{ Storage::url($politica['imagen']) }}" alt="{{ $politica['titulo'] }}" loading="lazy"></div>
                        @else
                            <div class="cm-card__icon"><i class="bi {{ $politica['icono'] }}"></i></div>
                        @endif
                        <h3>{{ $politica['titulo'] }}</h3>
                        <ul class="cm-list">
                            @foreach($politica['items'] as $item)
                                <li><i class="bi bi-check-circle-fill"></i> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Seguridad en obra --}}
@if($epp->isNotEmpty() || $protocolos->isNotEmpty() || $submodulos->isNotEmpty())
    <section class="cm-section cm-section--sand">
        <div class="cm-container cm-split">
            <div class="cm-sticky cm-reveal">
                <div class="cm-eyebrow">Seguridad en obra</div>
                <h2 class="cm-h2">Cada vaciado, <span>sin accidentes</span></h2>
                <p class="cm-lead">Nuestro personal trabaja con equipo de protección completo y protocolos claros en planta y en obra.</p>
            </div>
            <div style="display: grid; gap: 26px">
                @foreach([
                    ['Equipo de protección personal', 'bi-cone-striped', $epp],
                    ['Protocolos de seguridad', 'bi-clipboard-check', $protocolos],
                    ['Programas de gestión', 'bi-diagram-3', $submodulos],
                ] as [$titulo, $icono, $items])
                    @if($items->isNotEmpty())
                        <div class="cm-reveal">
                            <h3 style="font-size: 18px; font-weight: 800; color: var(--cm-ink); margin: 0 0 14px">{{ $titulo }}</h3>
                            <div class="cm-tags">
                                @foreach($items as $item)
                                    <span class="cm-tag"><i class="bi {{ $icono }}"></i> {{ $item }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Normas y recursos --}}
@if($recursos->isNotEmpty())
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-head cm-reveal">
                <div class="cm-eyebrow">Normas y referencias</div>
                <h2 class="cm-h2">Trabajamos <span>bajo norma</span></h2>
            </div>
            <div class="cm-grid-2">
                @foreach($recursos as $recurso)
                    @if(! empty($recurso['link']))
                        <a href="{{ $recurso['link'] }}" target="_blank" rel="noopener" class="cm-resource cm-reveal">
                    @else
                        <div class="cm-resource cm-reveal">
                    @endif
                        <div class="cm-resource__icon"><i class="bi bi-journal-bookmark"></i></div>
                        <div>
                            <strong>{{ $recurso['title'] }}</strong>
                            @if(! empty($recurso['source']))<small>{{ $recurso['source'] }}</small>@endif
                        </div>
                        @if(! empty($recurso['link']))<i class="bi bi-box-arrow-up-right" style="margin-left: auto; color: var(--cm-muted)"></i>@endif
                    @if(! empty($recurso['link']))
                        </a>
                    @else
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Más de la sección --}}
<section class="cm-section cm-section--sand">
    <div class="cm-container cm-grid-2">
        <a href="{{ route('experiencia') }}" class="cm-card cm-card--dark cm-reveal" style="text-decoration: none">
            <div class="cm-card__icon"><i class="bi bi-buildings"></i></div>
            <h3>Nuestra experiencia</h3>
            <p style="margin: 0 0 18px">Obras de vivienda, salud e infraestructura que llevan nuestro concreto.</p>
            <span class="cm-link-arrow">Ver proyectos <i class="bi bi-arrow-right"></i></span>
        </a>
        <a href="{{ route('concretips') }}" class="cm-card cm-reveal" style="text-decoration: none">
            <div class="cm-card__icon"><i class="bi bi-lightbulb"></i></div>
            <h3>Concretips</h3>
            <p style="margin: 0 0 18px; color: var(--cm-text)">Recomendaciones técnicas para que tu vaciado salga perfecto.</p>
            <span class="cm-link-arrow">Ver recomendaciones <i class="bi bi-arrow-right"></i></span>
        </a>
    </div>
</section>

@include('web.partials.cta-final', ['conFondo' => true])
@endsection
