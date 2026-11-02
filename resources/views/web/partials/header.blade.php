{{-- Encabezado de la web pública. Usa $config (Página de Inicio activa), definido en layouts.app --}}
@php
    $nombreMarca = trim($config->logo_text ?? '') ?: 'Concretera Mantaro';
    [$marca1, $marca2] = array_pad(explode(' ', $nombreMarca, 2), 2, '');
    $telefono = $config->oficina_celular ?? $config->oficina_telefono ?? null;
    $urlCotizar = request()->routeIs('home') ? '#cotizar' : route('home') . '#cotizar';
    $enlaces = [
        ['ruta' => 'home', 'texto' => 'Inicio', 'url' => route('home')],
        ['ruta' => 'nosotros', 'texto' => 'Nosotros', 'url' => route('nosotros')],
        ['ruta' => 'productos.*', 'texto' => 'Productos', 'url' => route('productos.index')],
    ];
    $sostenibilidadActiva = request()->routeIs('sostenibilidad', 'experiencia', 'concretips');
@endphp

<header class="cm-header" x-data="{ menu: false }">
    <div class="cm-container cm-header__inner">
        <a href="{{ route('home') }}" class="cm-brand" aria-label="{{ $nombreMarca }}, ir al inicio">
            @if($config && $config->logo_image)
                <img src="{{ Storage::url($config->logo_image) }}" alt="Logo {{ $nombreMarca }}">
            @endif
            <span>
                <span class="cm-brand__name">{{ $marca1 }} <span>{{ $marca2 }}</span></span>
                <span class="cm-brand__tag">Concreto premezclado</span>
            </span>
        </a>

        <nav class="cm-nav" aria-label="Menú principal">
            @foreach($enlaces as $enlace)
                <a href="{{ $enlace['url'] }}" class="cm-nav__link {{ request()->routeIs($enlace['ruta']) ? 'is-active' : '' }}">{{ $enlace['texto'] }}</a>
            @endforeach

            <div class="cm-dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <button type="button" class="cm-nav__link {{ $sostenibilidadActiva ? 'is-active' : '' }}" @click="open = !open" :aria-expanded="open">
                    Sostenibilidad <i class="bi bi-chevron-down" style="font-size: 12px"></i>
                </button>
                <div class="cm-dropdown__menu" x-show="open" x-transition.opacity.duration.150ms x-cloak>
                    <a href="{{ route('sostenibilidad') }}"><i class="bi bi-tree"></i> Políticas</a>
                    <a href="{{ route('experiencia') }}"><i class="bi bi-buildings"></i> Experiencia</a>
                    <a href="{{ route('concretips') }}"><i class="bi bi-lightbulb"></i> Concretips</a>
                </div>
            </div>

            <a href="{{ route('contacto.index') }}" class="cm-nav__link {{ request()->routeIs('contacto.*') ? 'is-active' : '' }}">Contacto</a>
        </nav>

        <div class="cm-header__actions">
            @if($telefono)
                <a href="tel:{{ preg_replace('/\D/', '', $telefono) }}" class="cm-header__phone">
                    <small>Llámanos</small>
                    <strong>{{ $telefono }}</strong>
                </a>
            @endif

            <a href="{{ $urlCotizar }}" class="cm-btn cm-btn--primary cm-btn--sm">
                <i class="bi bi-calculator"></i> Cotizar
            </a>

            @auth
                <a href="{{ url('/admin') }}" class="cm-header__login" title="Ir al sistema"><i class="bi bi-speedometer2"></i></a>
            @else
                <a href="{{ url('/admin/login') }}" class="cm-header__login" title="Intranet (personal de la empresa)"><i class="bi bi-person"></i></a>
            @endauth

            <button type="button" class="cm-burger" @click="menu = !menu" :aria-expanded="menu" aria-label="Abrir menú">
                <i class="bi" :class="menu ? 'bi-x-lg' : 'bi-list'"></i>
            </button>
        </div>
    </div>

    {{-- Menú en celular --}}
    <div class="cm-mobile" :class="{ 'is-open': menu }">
        <a href="{{ route('home') }}"><i class="bi bi-house"></i> Inicio</a>
        <a href="{{ route('nosotros') }}"><i class="bi bi-people"></i> Nosotros</a>
        <a href="{{ route('productos.index') }}"><i class="bi bi-box-seam"></i> Productos</a>
        <a href="{{ route('sostenibilidad') }}"><i class="bi bi-tree"></i> Sostenibilidad</a>
        <a href="{{ route('experiencia') }}"><i class="bi bi-buildings"></i> Experiencia</a>
        <a href="{{ route('concretips') }}"><i class="bi bi-lightbulb"></i> Concretips</a>
        <a href="{{ route('contacto.index') }}"><i class="bi bi-envelope"></i> Contacto</a>
        @if($telefono)
            <a href="tel:{{ preg_replace('/\D/', '', $telefono) }}"><i class="bi bi-telephone"></i> {{ $telefono }}</a>
        @endif
        <a href="{{ $urlCotizar }}" class="cm-btn cm-btn--primary cm-btn--block" @click="menu = false">
            <i class="bi bi-calculator"></i> Cotiza tu obra
        </a>
    </div>
</header>
