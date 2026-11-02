{{-- Pie de la web pública. Usa $config (Página de Inicio activa), definido en layouts.app --}}
@php
    $nombreMarca = trim($config->logo_text ?? '') ?: 'Concretera Mantaro';
    $whatsappPlanta = $config->planta_whatsapp ?? null;
@endphp

<footer class="cm-footer">
    <div class="cm-container cm-footer__grid">
        <div>
            <a href="{{ route('home') }}" class="cm-brand">
                @if($config && $config->logo_image)
                    <img src="{{ Storage::url($config->logo_image) }}" alt="Logo {{ $nombreMarca }}">
                @endif
                <span class="cm-brand__name">{{ $nombreMarca }}</span>
            </a>
            <p class="cm-footer__about">Concreto premezclado de calidad para obras de vivienda, infraestructura e industria en el Valle del Mantaro. Tu proyecto, nuestro legado.</p>
            @if($config && $config->social_networks)
                <div class="cm-social">
                    @foreach($config->social_networks as $social)
                        @if(! empty($social['url']))
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['name'] ?? 'Red social' }}"><i class="{{ $social['icon'] ?? 'bi bi-link-45deg' }}"></i></a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h4>{{ $config->oficina_titulo ?? 'Oficina' }}</h4>
            @if($config?->oficina_direccion)
                <p class="cm-footer__item"><i class="bi bi-geo-alt"></i>
                    <span>{{ $config->oficina_direccion }}@if($config->oficina_maps_url) · <a href="{{ $config->oficina_maps_url }}" target="_blank" rel="noopener">Ver mapa</a>@endif</span>
                </p>
            @endif
            @if($config?->oficina_telefono)
                <p class="cm-footer__item"><i class="bi bi-telephone"></i> <a href="tel:{{ preg_replace('/\D/', '', $config->oficina_telefono) }}">{{ $config->oficina_telefono }}</a></p>
            @endif
            @if($config?->oficina_celular)
                <p class="cm-footer__item"><i class="bi bi-phone"></i> <a href="tel:{{ preg_replace('/\D/', '', $config->oficina_celular) }}">{{ $config->oficina_celular }}</a></p>
            @endif
            @if($config?->oficina_email)
                <p class="cm-footer__item"><i class="bi bi-envelope"></i> <a href="mailto:{{ $config->oficina_email }}">{{ $config->oficina_email }}</a></p>
            @endif
        </div>

        <div>
            <h4>{{ $config->planta_titulo ?? 'Planta de producción' }}</h4>
            @if($config?->planta_ubicacion)
                <p class="cm-footer__item"><i class="bi bi-geo-alt"></i> <span>{{ $config->planta_ubicacion }}</span></p>
            @endif
            @if($config?->planta_maps_url)
                <p class="cm-footer__item"><i class="bi bi-map"></i> <a href="{{ $config->planta_maps_url }}" target="_blank" rel="noopener">Ver planta en Google Maps</a></p>
            @endif
            @if($whatsappPlanta)
                <p class="cm-footer__item"><i class="bi bi-whatsapp"></i> <a href="{{ App\Models\Inicio::enlaceWhatsapp($whatsappPlanta) }}" target="_blank" rel="noopener">{{ $whatsappPlanta }}</a></p>
            @endif
        </div>

        <div>
            <h4>{{ $config->atencion_titulo ?? 'Atención al cliente' }}</h4>
            <p class="cm-footer__item"><i class="bi bi-clock"></i> <span>Lunes a sábado<br>{{ $config->atencion_horario_lunes_sabado ?? '8:00 a.m. – 6:00 p.m.' }}</span></p>
            <p class="cm-footer__item"><i class="bi bi-calendar-event"></i> <span>Domingos<br>{{ $config->atencion_horario_domingo ?? 'Previa coordinación' }}</span></p>
            @if($config?->atencion_asesora_comercial)
                <p class="cm-footer__item"><i class="bi bi-person-badge"></i> <span>Asesoría comercial<br><a href="tel:{{ preg_replace('/\D/', '', $config->atencion_asesora_comercial) }}">{{ $config->atencion_asesora_comercial }}</a></span></p>
            @endif
        </div>
    </div>

    <div class="cm-footer__bottom">
        <div class="cm-container">
            <span>© {{ date('Y') }} {{ $nombreMarca }}. Todos los derechos reservados.</span>
            {{-- Aviso obligatorio del Libro de Reclamaciones (Ley 29571) --}}
            <a href="{{ route('reclamaciones.index') }}" class="cm-libro">
                <i class="bi bi-journal-text"></i>
                <span>Libro de Reclamaciones <small>Conforme al Código de Protección al Consumidor</small></span>
            </a>
        </div>
    </div>
</footer>
