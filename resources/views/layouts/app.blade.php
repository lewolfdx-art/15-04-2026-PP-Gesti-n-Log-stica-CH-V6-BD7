@php
    $config = App\Models\Inicio::getActiveContent();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Concretera Mantaro')</title>

    <!-- ========== FAVICON ========== -->
    @if($config && $config->favicon)
        <link rel="icon" type="image/png" href="{{ Storage::url($config->favicon) }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ Storage::url($config->favicon) }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ Storage::url($config->favicon) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($config->favicon) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    @endif
    <meta name="msapplication-TileColor" content="#16222e">
    <meta name="theme-color" content="#16222e">

    <!-- Íconos y Alpine.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <!-- Diseño de la web (resources/css/web.css) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/web.css')

    <style>
        [x-cloak] { display: none !important; }

        /* Colores que usan los estilos propios del Libro de Reclamaciones */
        :root {
            --color-primary: #f5811e;
            --color-dark: #16222e;
            --color-white: #fcfcfc;
        }

        /* Botón flotante de WhatsApp */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            background-color: #128C7E;
            color: white;
        }
        @media (max-width: 768px) {
            .whatsapp-float { width: 50px; height: 50px; font-size: 1.5rem; bottom: 20px; right: 20px; }
        }
    </style>

    @yield('styles')
</head>
<body class="cm-page">

@include('web.partials.header')

<!-- Contenido principal -->
@hasSection('pagina_completa')
    {{-- Páginas con el diseño nuevo: secciones a todo el ancho --}}
    <main>
        @yield('content')
    </main>
@else
    <main class="cm-section">
        <div class="cm-container">
            @yield('content')
        </div>
    </main>
@endif

@include('web.partials.footer')


<script>
    // Aparición suave de las secciones y conteo de las cifras al entrar en pantalla
    document.addEventListener('DOMContentLoaded', function () {
        var observer = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (!entrada.isIntersecting) return;
                var el = entrada.target;
                el.classList.add('is-visible');
                el.querySelectorAll('[data-contar]').forEach(function (num) {
                    var fin = parseFloat(num.dataset.contar);
                    if (isNaN(fin)) return;
                    var inicio = null;
                    var paso = function (t) {
                        if (!inicio) inicio = t;
                        var p = Math.min((t - inicio) / 1200, 1);
                        num.textContent = Math.round(fin * (1 - Math.pow(1 - p, 3))).toLocaleString('es-PE');
                        if (p < 1) requestAnimationFrame(paso);
                    };
                    requestAnimationFrame(paso);
                });
                observer.unobserve(el);
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.cm-reveal').forEach(function (el) { observer.observe(el); });

        // Encabezado compacto y barra de progreso al bajar (un solo cálculo por cuadro)
        var header = document.querySelector('.cm-header');
        var barra = document.querySelector('.cm-progress');
        var pendiente = false;
        var actualizar = function () {
            var y = window.scrollY;
            var alto = document.documentElement.scrollHeight - window.innerHeight;
            if (header) header.classList.toggle('is-scrolled', y > 20);
            if (barra) barra.style.transform = 'scaleX(' + (alto > 0 ? Math.min(y / alto, 1) : 0) + ')';
            pendiente = false;
        };
        window.addEventListener('scroll', function () {
            if (!pendiente) { pendiente = true; requestAnimationFrame(actualizar); }
        }, { passive: true });
        actualizar();
    });
</script>

<!-- Botón flotante de WhatsApp en todas las páginas (número editable en Página de Inicio) -->
@php($enlaceWhatsapp = App\Models\Inicio::enlaceWhatsapp($config->whatsapp_number ?? null, 'Hola Concretera Mantaro, quiero información sobre concreto premezclado.'))
@if($enlaceWhatsapp)
    <a href="{{ $enlaceWhatsapp }}" class="whatsapp-float" target="_blank" rel="noopener" title="Escríbenos por WhatsApp" aria-label="Escríbenos por WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
@endif

@yield('scripts')
</body>
</html>