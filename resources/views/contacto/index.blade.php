@extends('layouts.app')

@section('title', 'Contacto | Concretera Mantaro')

@section('pagina_completa', true)

@section('content')
@php
    $config = App\Models\Inicio::getActiveContent();
    $enlaceWa = App\Models\Inicio::enlaceWhatsapp($config->whatsapp_number ?? null, 'Hola, quiero información sobre concreto premezclado.');
    $telefonos = collect([$config?->oficina_telefono, $config?->oficina_celular, $config?->atencion_asesora_comercial])->filter()->unique();
@endphp

@include('web.partials.page-hero', [
    'titulo' => 'Hablemos de',
    'resaltado' => 'tu obra',
    'texto' => 'Escríbenos o llámanos. Respondemos el mismo día en horario de atención.',
    'miga' => 'Contacto',
])

<section class="cm-section cm-section--sand">
    <div class="cm-container cm-contact">
        {{-- Formulario --}}
        <div class="cm-form-card cm-reveal">
            <h2>Envíanos un mensaje</h2>
            <p>Para cotizar más rápido usa el formulario <a href="{{ route('home') }}#cotizar" style="color: var(--cm-orange); font-weight: 700">Cotiza tu obra</a>.</p>

            @if(session('success'))
                <div class="cm-alert"><i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}. Te responderemos muy pronto.</span></div>
            @endif
            @error('general')
                <div class="cm-alert cm-alert--error"><i class="bi bi-exclamation-circle-fill"></i> <span>{{ $message }}</span></div>
            @enderror

            <form action="{{ route('contacto.store') }}" method="POST">
                @csrf
                <div class="cm-form-grid">
                    <div class="cm-field">
                        <label for="name">Nombre completo *</label>
                        <input type="text" id="name" name="name" required maxlength="255" autocomplete="name" value="{{ old('name') }}" placeholder="Ej: Andrés Rodríguez">
                        @error('name')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field">
                        <label for="phone">Celular *</label>
                        <input type="tel" id="phone" name="phone" required maxlength="30" autocomplete="tel" value="{{ old('phone') }}" placeholder="9XX XXX XXX">
                        @error('phone')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <label for="email">Correo electrónico *</label>
                        <input type="email" id="email" name="email" required maxlength="255" autocomplete="email" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com">
                        @error('email')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <label for="subject">Asunto *</label>
                        <input type="text" id="subject" name="subject" required maxlength="255" value="{{ old('subject') }}" placeholder="Ej: Cotización de concreto para losa">
                        @error('subject')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <label for="message">Mensaje *</label>
                        <textarea id="message" name="message" rows="5" required maxlength="5000" placeholder="Cuéntanos qué necesitas">{{ old('message') }}</textarea>
                        @error('message')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <button type="submit" class="cm-btn cm-btn--primary"><i class="bi bi-send"></i> Enviar mensaje</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Datos de contacto --}}
        <div class="cm-places">
            @if($enlaceWa)
                <a href="{{ $enlaceWa }}" target="_blank" rel="noopener" class="cm-place cm-reveal" style="text-decoration: none; background: var(--cm-wa)">
                    <div class="cm-place__icon" style="background: rgba(255,255,255,.18); color: #fff"><i class="bi bi-whatsapp"></i></div>
                    <div>
                        <h3 style="color: #fff">Escríbenos por WhatsApp</h3>
                        <p style="color: rgba(255,255,255,.9); margin: 0">Te respondemos al toque</p>
                    </div>
                </a>
            @endif
            @if($telefonos->isNotEmpty())
                <div class="cm-place cm-reveal">
                    <div class="cm-place__icon"><i class="bi bi-telephone"></i></div>
                    <div>
                        <h3>Llámanos</h3>
                        @foreach($telefonos as $telefono)
                            <p style="margin: 0 0 4px"><a href="tel:{{ preg_replace('/\D/', '', $telefono) }}">{{ $telefono }}</a></p>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($config?->oficina_email)
                <div class="cm-place cm-reveal">
                    <div class="cm-place__icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <h3>Correo</h3>
                        <p style="margin: 0"><a href="mailto:{{ $config->oficina_email }}">{{ $config->oficina_email }}</a></p>
                    </div>
                </div>
            @endif
            @if($config?->oficina_direccion)
                <div class="cm-place cm-reveal">
                    <div class="cm-place__icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <h3>{{ $config->oficina_titulo ?? 'Oficina' }}</h3>
                        <p>{{ $config->oficina_direccion }}</p>
                        @if($config->oficina_maps_url)<a href="{{ $config->oficina_maps_url }}" target="_blank" rel="noopener">Cómo llegar <i class="bi bi-arrow-up-right"></i></a>@endif
                    </div>
                </div>
            @endif
            <div class="cm-place cm-reveal">
                <div class="cm-place__icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h3>Horario</h3>
                    <p style="margin: 0">Lunes a sábado: {{ $config->atencion_horario_lunes_sabado ?? '8:00 a.m. – 6:00 p.m.' }}<br>Domingos: {{ $config->atencion_horario_domingo ?? 'previa coordinación' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
