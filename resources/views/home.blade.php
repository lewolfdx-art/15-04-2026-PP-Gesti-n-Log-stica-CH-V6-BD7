@extends('layouts.app')

@section('title', 'Concreto premezclado en Huancayo | ' . (trim($inicio->logo_text ?? '') ?: 'Concretera Mantaro'))

{{-- Diseño nuevo: secciones a todo el ancho --}}
@section('pagina_completa', true)

@section('content')
@php
    $marca = trim($inicio->logo_text ?? '') ?: 'Concretera Mantaro';
    $beneficios = $inicio?->beneficios ?? [];
    $obras = collect($inicio?->proyectos_recientes ?? [])->filter(fn ($o) => filled($o['title'] ?? null));
    $operaciones = collect($inicio?->nuestras_operaciones ?? [])->filter(fn ($o) => filled($o['title'] ?? null));
    $cifras = collect($inicio?->cifras ?? [])->filter(fn ($c) => filled($c['valor'] ?? null))->take(6);
    $zonas = collect($inicio?->zonas_cobertura ?? [])->filter();
    $whatsapp = App\Models\Inicio::enlaceWhatsapp($inicio->whatsapp_number ?? null, 'Hola, quiero cotizar concreto premezclado para mi obra.');
    $iconosOperacion = ['bi-buildings', 'bi-truck', 'bi-clipboard2-pulse', 'bi-bricks', 'bi-people'];
@endphp

{{-- ============ PORTADA ============ --}}
<section class="cm-hero" @if($inicio?->hero_image) style="background-image: url('{{ Storage::url($inicio->hero_image) }}')" @endif>
    <div class="cm-container cm-hero__inner">
        <div>
            <div class="cm-hero__badge"><b>Valle del Mantaro</b> Concreto premezclado para tu obra</div>
            <h1>{!! str($inicio->hero_title ?? 'Concreto <span>premezclado</span> de alta calidad')->sanitizeHtml() !!}</h1>
            @if($inicio?->hero_description)
                <p class="cm-hero__text">{{ $inicio->hero_description }}</p>
            @endif
            <div class="cm-hero__buttons">
                <a href="#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> {{ $inicio->cta_text ?? 'Cotiza tu obra' }}</a>
                @if($whatsapp)
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="cm-btn cm-btn--ghost"><i class="bi bi-whatsapp"></i> Escríbenos por WhatsApp</a>
                @endif
            </div>
            <ul class="cm-hero__checks">
                <li><i class="bi bi-patch-check-fill"></i> Visita técnica sin costo</li>
                <li><i class="bi bi-clock-fill"></i> Entrega puntual programada</li>
                <li><i class="bi bi-shield-fill-check"></i> Control de calidad en cada vaciado</li>
            </ul>
        </div>

        {{-- Cotización rápida: llega a Mensajes de contacto y avisa a los administradores --}}
        <div class="cm-quote" id="cotizar">
            <h2 class="cm-quote__title">Cotiza tu obra</h2>
            <p class="cm-quote__sub">Déjanos tus datos y un asesor te llama para coordinar la visita técnica.</p>

            @if(session('cotizacion_enviada'))
                <div class="cm-alert"><i class="bi bi-check-circle-fill"></i>
                    <span>¡Listo! Recibimos tu solicitud. Un asesor te llamará muy pronto.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('cotizacion-rapida.store') }}" novalidate>
                @csrf
                <div class="cm-trap" aria-hidden="true">
                    <label>No llenar <input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label>
                </div>
                <div class="cm-form-grid">
                    <div class="cm-field">
                        <label for="q-nombre">Nombre *</label>
                        <input id="q-nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="120" autocomplete="name" placeholder="Tu nombre">
                        @error('nombre')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field">
                        <label for="q-telefono">Celular *</label>
                        <input id="q-telefono" name="telefono" value="{{ old('telefono') }}" required inputmode="tel" autocomplete="tel" placeholder="9XX XXX XXX">
                        @error('telefono')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <label for="q-lugar">Lugar de la obra *</label>
                        <input id="q-lugar" name="lugar" value="{{ old('lugar') }}" required maxlength="160" placeholder="Distrito y referencia (ej. El Tambo, Av. Huancavelica)">
                        @error('lugar')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field">
                        <label for="q-volumen">m³ aproximados</label>
                        <input id="q-volumen" name="volumen" type="number" step="0.5" min="0.5" value="{{ old('volumen') }}" placeholder="Ej. 12">
                        @error('volumen')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field">
                        <label for="q-fecha">Fecha tentativa</label>
                        <input id="q-fecha" name="fecha" type="date" min="{{ today()->toDateString() }}" value="{{ old('fecha') }}">
                        @error('fecha')<span class="cm-field__error">{{ $message }}</span>@enderror
                    </div>
                    <div class="cm-field cm-field--full">
                        <label for="q-estructura">¿Qué vas a vaciar?</label>
                        <select id="q-estructura" name="estructura">
                            <option value="">Selecciona (opcional)</option>
                            @foreach($estructuras as $valor => $texto)
                                <option value="{{ $valor }}" @selected(old('estructura') === $valor)>{{ ucfirst(mb_strtolower($texto)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cm-field cm-field--full">
                        <button type="submit" class="cm-btn cm-btn--primary cm-btn--block"><i class="bi bi-send"></i> Solicitar cotización</button>
                    </div>
                </div>
                <p class="cm-quote__foot"><i class="bi bi-lock"></i> Solo usamos tus datos para contactarte sobre tu obra.</p>
            </form>
        </div>
    </div>
</section>

{{-- ============ CIFRAS ============ --}}
@if($cifras->isNotEmpty())
<section class="cm-stats">
    <div class="cm-container">
        <div class="cm-stats__grid cm-reveal" style="--cm-cols: {{ min($cifras->count(), 4) }}">
            @foreach($cifras as $cifra)
                @php($numero = is_numeric($cifra['valor']) ? (float) $cifra['valor'] : null)
                <div class="cm-stat">
                    <div class="cm-stat__value"><span @if($numero !== null) data-contar="{{ $numero }}" @endif>{{ $cifra['valor'] }}</span><small>{{ $cifra['sufijo'] ?? '' }}</small></div>
                    <div class="cm-stat__label">{{ $cifra['etiqueta'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ PRODUCTOS ============ --}}
@if($tiposConcreto->isNotEmpty())
<section class="cm-section">
    <div class="cm-container">
        <div class="cm-head cm-reveal">
            <div class="cm-eyebrow">Nuestros productos</div>
            <h2 class="cm-h2">Concreto para cada <span>etapa de tu obra</span></h2>
            <p class="cm-lead">Desde cimientos hasta losas y estructuras de gran exigencia: diseñamos la mezcla que tu proyecto necesita.</p>
        </div>
        <div class="cm-grid-3">
            @foreach($tiposConcreto as $producto)
                <a href="{{ route('productos.show', Str::slug($producto['name'])) }}" class="cm-product cm-reveal">
                    @if(! empty($producto['image']))
                        <div class="cm-product__img"><img src="{{ Storage::url($producto['image']) }}" alt="Concreto {{ $producto['name'] }}" loading="lazy"></div>
                    @else
                        <div class="cm-product__img cm-product__img--empty"><i class="bi bi-box-seam"></i></div>
                    @endif
                    <div class="cm-product__body">
                        <h3 class="cm-product__name">{{ $producto['name'] }}</h3>
                        <div class="cm-product__desc">{{ Str::limit(strip_tags($producto['description'] ?? ''), 160) }}</div>
                        <span class="cm-link-arrow">Ver detalle <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="cm-center">
            <a href="{{ route('productos.index') }}" class="cm-btn cm-btn--dark">Ver todos los productos y servicios <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

{{-- ============ CÓMO TRABAJAMOS ============ --}}
<section class="cm-section cm-section--dark">
    <div class="cm-container">
        <div class="cm-head cm-reveal">
            <div class="cm-eyebrow">Cómo trabajamos</div>
            <h2 class="cm-h2">Tu vaciado en <span>4 pasos</span></h2>
            <p class="cm-lead">Un proceso claro, de la primera llamada al último m³ en tu obra.</p>
        </div>
        <div class="cm-steps">
            @foreach([
                ['bi-chat-dots', 'Cotizas', 'Nos cuentas qué vas a vaciar, cuántos m³ y dónde. Te respondemos el mismo día.'],
                ['bi-rulers', 'Visita técnica', 'Un asesor mide la obra, revisa el acceso para el mixer o la bomba y te propone la mezcla ideal.'],
                ['bi-calendar-check', 'Programamos', 'Reservamos fecha y hora, y asignamos los mixers y la bomba para que nada se cruce.'],
                ['bi-truck', 'Vaciamos', 'Llegamos puntuales con el concreto controlado en planta y te acompañamos hasta el final.'],
            ] as $i => [$icono, $titulo, $texto])
                <div class="cm-step cm-reveal" style="transition-delay: {{ $i * 90 }}ms">
                    <div class="cm-step__num"><div class="cm-step__icon"><i class="bi {{ $icono }}"></i></div><span>0{{ $i + 1 }}</span></div>
                    <h3>{{ $titulo }}</h3>
                    <p>{{ $texto }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ POR QUÉ ELEGIRNOS ============ --}}
@if(! empty($beneficios))
<section class="cm-section cm-section--sand">
    <div class="cm-container cm-split">
        <div class="cm-sticky cm-reveal">
            <div class="cm-eyebrow">Por qué elegirnos</div>
            <h2 class="cm-h2">Calidad que se nota <span>en cada vaciado</span></h2>
            <p class="cm-lead">Producimos, transportamos y vaciamos con equipo propio y personal técnico, para que tu obra avance sin sorpresas.</p>
            <ul class="cm-checklist">
                <li><i class="bi bi-check-circle-fill"></i> Mezclas diseñadas según la estructura</li>
                <li><i class="bi bi-check-circle-fill"></i> Mixers y bombas propios</li>
                <li><i class="bi bi-check-circle-fill"></i> Asesoría técnica antes y durante el vaciado</li>
            </ul>
            <a href="#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> Cotiza tu obra</a>
        </div>
        <div class="cm-features">
            @foreach($beneficios as $beneficio)
                <div class="cm-feature cm-reveal">
                    <div class="cm-feature__icon"><i class="bi {{ $beneficio['icon'] ?? 'bi-check2-circle' }}"></i></div>
                    <h3>{{ $beneficio['title'] ?? '' }}</h3>
                    <p>{{ $beneficio['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ OBRAS ============ --}}
@if($obras->isNotEmpty())
<section class="cm-section">
    <div class="cm-container">
        <div class="cm-head cm-reveal">
            <div class="cm-eyebrow">Obras realizadas</div>
            <h2 class="cm-h2">Proyectos que <span>llevan nuestro concreto</span></h2>
        </div>
        <div class="cm-gallery">
            @foreach($obras as $obra)
                <div class="cm-tile cm-reveal">
                    @if(! empty($obra['image']))
                        <img src="{{ Storage::url($obra['image']) }}" alt="{{ $obra['title'] }}" loading="lazy">
                    @else
                        <i class="bi bi-building cm-tile__icon"></i>
                    @endif
                    <span class="cm-tile__tag">Obra</span>
                    <div class="cm-tile__caption">
                        <h3>{{ $obra['title'] }}</h3>
                        @if(! empty($obra['description']))<p>{{ $obra['description'] }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cm-center">
            <a href="{{ route('experiencia') }}" class="cm-btn cm-btn--dark">Ver nuestra experiencia <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

{{-- ============ OPERACIONES ============ --}}
@if($operaciones->isNotEmpty())
<section class="cm-section cm-section--sand">
    <div class="cm-container">
        <div class="cm-head cm-reveal">
            <div class="cm-eyebrow">Nuestras operaciones</div>
            <h2 class="cm-h2">Planta, flota y <span>laboratorio propios</span></h2>
        </div>
        <div class="cm-gallery">
            @foreach($operaciones as $i => $operacion)
                <div class="cm-tile cm-reveal">
                    @if(! empty($operacion['image']))
                        <img src="{{ Storage::url($operacion['image']) }}" alt="{{ $operacion['title'] }}" loading="lazy">
                    @else
                        <i class="bi {{ $iconosOperacion[$i % count($iconosOperacion)] }} cm-tile__icon"></i>
                    @endif
                    <div class="cm-tile__caption">
                        <h3>{{ $operacion['title'] }}</h3>
                        @if(! empty($operacion['description']))<p>{{ $operacion['description'] }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ COBERTURA ============ --}}
<section class="cm-section">
    <div class="cm-container cm-coverage">
        <div class="cm-reveal">
            <div class="cm-eyebrow">Cobertura</div>
            <h2 class="cm-h2">Llegamos <span>a tu obra</span></h2>
            <p class="cm-lead">Atendemos obras en todo el Valle del Mantaro. Si tu obra está en otra zona, consúltanos: coordinamos el despacho.</p>
            @if($zonas->isNotEmpty())
                <div class="cm-chips">
                    @foreach($zonas as $zona)
                        <span class="cm-chip"><i class="bi bi-geo-alt-fill"></i> {{ $zona }}</span>
                    @endforeach
                </div>
            @endif
            <a href="#cotizar" class="cm-btn cm-btn--primary"><i class="bi bi-calculator"></i> Cotiza tu obra</a>
        </div>
        <div class="cm-places">
            @if($inicio?->planta_ubicacion || $inicio?->planta_maps_url)
                <div class="cm-place cm-reveal">
                    <div class="cm-place__icon"><i class="bi bi-buildings"></i></div>
                    <div>
                        <h3>{{ $inicio->planta_titulo ?? 'Planta de producción' }}</h3>
                        @if($inicio->planta_ubicacion)<p>{{ $inicio->planta_ubicacion }}</p>@endif
                        @if($inicio->planta_maps_url)<a href="{{ $inicio->planta_maps_url }}" target="_blank" rel="noopener">Cómo llegar <i class="bi bi-arrow-up-right"></i></a>@endif
                    </div>
                </div>
            @endif
            @if($inicio?->oficina_direccion)
                <div class="cm-place cm-reveal">
                    <div class="cm-place__icon"><i class="bi bi-briefcase"></i></div>
                    <div>
                        <h3>{{ $inicio->oficina_titulo ?? 'Oficina' }}</h3>
                        <p>{{ $inicio->oficina_direccion }}</p>
                        @if($inicio->oficina_maps_url)<a href="{{ $inicio->oficina_maps_url }}" target="_blank" rel="noopener">Cómo llegar <i class="bi bi-arrow-up-right"></i></a>@endif
                    </div>
                </div>
            @endif
            <div class="cm-place cm-reveal">
                <div class="cm-place__icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h3>Horario de atención</h3>
                    <p>Lunes a sábado: {{ $inicio->atencion_horario_lunes_sabado ?? '8:00 a.m. – 6:00 p.m.' }}<br>Domingos: {{ $inicio->atencion_horario_domingo ?? 'previa coordinación' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ LLAMADO FINAL ============ --}}
<section class="cm-section" style="padding-top: 0">
    <div class="cm-container">
        <div class="cm-cta cm-reveal">
            <div>
                <h2>¿Listo para vaciar tu obra?</h2>
                <p>Cuéntanos tu proyecto y te enviamos una cotización a la medida.</p>
                <div class="cm-cta__buttons">
                    <a href="#cotizar" class="cm-btn cm-btn--dark"><i class="bi bi-calculator"></i> Solicitar cotización</a>
                    @if($whatsapp)
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="cm-btn cm-btn--ghost"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                    @endif
                </div>
            </div>
            <div class="cm-cta__art" aria-hidden="true">
                <img src="{{ asset('img/mixer.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
@endsection
