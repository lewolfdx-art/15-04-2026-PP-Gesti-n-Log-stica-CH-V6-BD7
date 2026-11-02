@extends('layouts.app')

@section('title', 'Productos y servicios | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $productos = App\Models\Producto::getActiveContent();
    $tiposConcreto = collect($productos?->tipos_concreto ?? [])->filter(fn ($p) => filled($p['name'] ?? null));
    $servicios = collect($productos?->servicios_complementarios ?? [])->filter(fn ($s) => filled($s['name'] ?? null));
    $iconosServicio = ['bi-box-seam', 'bi-truck', 'bi-water', 'bi-tools'];
@endphp

@include('web.partials.page-hero', [
    'titulo' => strip_tags($productos->page_title ?? 'Nuestros productos y servicios'),
    'texto' => strip_tags($productos->page_description ?? 'Concreto premezclado y servicios complementarios para cada etapa de tu obra.'),
    'miga' => 'Productos',
    'acciones' => true,
])

{{-- Tipos de concreto. Cada tarjeta tiene id = slug: /productos/{slug} lleva aquí --}}
@if($tiposConcreto->isNotEmpty())
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-head cm-reveal">
                <div class="cm-eyebrow">Tipos de concreto</div>
                <h2 class="cm-h2">La mezcla correcta <span>para tu estructura</span></h2>
                <p class="cm-lead">Diseñamos y controlamos cada mezcla en laboratorio según la resistencia y las condiciones de tu obra.</p>
            </div>
            <div class="cm-grid-3">
                @foreach($tiposConcreto as $producto)
                    <article class="cm-product cm-reveal" id="{{ Str::slug($producto['name']) }}" style="scroll-margin-top: 110px">
                        @if(! empty($producto['image']))
                            <div class="cm-product__img"><img src="{{ Storage::url($producto['image']) }}" alt="Concreto {{ $producto['name'] }}" loading="lazy"></div>
                        @else
                            <div class="cm-product__img cm-product__img--empty"><i class="bi bi-box-seam"></i></div>
                        @endif
                        <div class="cm-product__body">
                            <h3 class="cm-product__name">{{ $producto['name'] }}</h3>
                            @if(! empty($producto['price']))
                                <span class="cm-price">Desde S/ {{ number_format($producto['price'], 2) }} por m³</span>
                            @endif
                            <div class="cm-prose" style="font-size: 15px; line-height: 1.65; margin-bottom: 18px">{!! str($producto['description'] ?? '')->sanitizeHtml() !!}</div>
                            <a href="{{ route('home') }}#cotizar" class="cm-link-arrow" style="text-decoration: none">Cotizar este concreto</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Servicios --}}
@if($servicios->isNotEmpty())
    <section class="cm-section cm-section--sand">
        <div class="cm-container">
            <div class="cm-head cm-reveal">
                <div class="cm-eyebrow">Servicios</div>
                <h2 class="cm-h2">Todo para tu vaciado <span>en un solo lugar</span></h2>
            </div>
            @foreach($servicios as $i => $servicio)
                <div class="cm-media {{ $i % 2 ? 'cm-media--flip' : '' }} cm-reveal" id="{{ Str::slug($servicio['name']) }}" style="scroll-margin-top: 110px">
                    <div>
                        <div class="cm-card__icon"><i class="bi {{ $iconosServicio[$i % count($iconosServicio)] }}"></i></div>
                        <h2 class="cm-h2" style="font-size: clamp(26px, 3vw, 34px)">{{ $servicio['name'] }}</h2>
                        @if(! empty($servicio['price']))
                            <span class="cm-price">Desde S/ {{ number_format($servicio['price'], 2) }}</span>
                        @endif
                        <div class="cm-prose" style="margin-bottom: 26px">{!! str($servicio['description'] ?? '')->sanitizeHtml() !!}</div>
                        <a href="{{ route('home') }}#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> Solicitar este servicio</a>
                    </div>
                    @if(! empty($servicio['image']))
                        <div class="cm-media__img"><img src="{{ Storage::url($servicio['image']) }}" alt="{{ $servicio['name'] }}" loading="lazy"></div>
                    @else
                        <div class="cm-media__img cm-media__img--empty"><i class="bi {{ $iconosServicio[$i % count($iconosServicio)] }}"></i></div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endif

@if($tiposConcreto->isEmpty() && $servicios->isEmpty())
    <section class="cm-section"><div class="cm-container cm-head"><p class="cm-lead">Pronto publicaremos nuestros productos.</p></div></section>
@endif

@include('web.partials.cta-final', [
    'tituloCta' => '¿No sabes qué concreto necesitas?',
    'textoCta' => 'Un asesor visita tu obra sin costo y te recomienda la mezcla ideal.',
])
@endsection
