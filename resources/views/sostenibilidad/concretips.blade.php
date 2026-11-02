@extends('layouts.app')

@section('title', 'Concretips: recomendaciones técnicas | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $tips = collect($recomendaciones ?? [])->filter(fn ($r) => filled($r['title'] ?? null))->values();
@endphp

@include('web.partials.page-hero', [
    'titulo' => 'Concretips:',
    'resaltado' => 'vacía como un experto',
    'texto' => 'Recomendaciones técnicas de nuestros ingenieros para que tu concreto alcance la resistencia que tu obra necesita.',
    'miga' => 'Concretips',
])

<section class="cm-section cm-section--sand">
    <div class="cm-container cm-narrow">
        @forelse($tips as $i => $tip)
            <article class="cm-tip cm-reveal">
                <div class="cm-tip__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                <div>
                    <h3>{{ $tip['title'] }}</h3>
                    <div class="cm-prose" style="font-size: 16px">{!! str($tip['description'] ?? '')->sanitizeHtml() !!}</div>
                    @if(! empty($tip['image']))
                        <div class="cm-tip__img"><img src="{{ Storage::url($tip['image']) }}" alt="{{ $tip['title'] }}" loading="lazy"></div>
                    @endif
                </div>
            </article>
        @empty
            <div class="cm-head"><p class="cm-lead">Pronto publicaremos nuestras recomendaciones.</p></div>
        @endforelse
    </div>
</section>

@include('web.partials.cta-final', [
    'conFondo' => true,
    'tituloCta' => '¿Tienes dudas técnicas sobre tu vaciado?',
    'textoCta' => 'Nuestros asesores te ayudan a elegir la mezcla y a planificar el vaciado sin costo.',
])
@endsection
