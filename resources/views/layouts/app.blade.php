@php
    $config = App\Models\Inicio::getActiveContent();
    $isHomePage = request()->path() === '/';
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

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <!-- Diseño nuevo de la web (resources/css/web.css) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/web.css')
    <style>[x-cloak] { display: none !important; }</style>

    <style>
        /* Variables de color corporativas */
        :root {
            --color-primary: #f5811e;
            --color-primary-dark: #e06e0a;
            --color-white: #fcfcfc;
            --color-dark: #16222e;
            --color-gold: #ddb31f;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--color-white);
            color: var(--color-dark);
        }

        main {
            flex: 1;
            width: 100%;
        }

        /* ========== EFECTOS NEÓN ========== */
        
        .neon-glow-orange {
            text-shadow: 0 0 10px rgba(245, 129, 30, 0.9),
                         0 0 20px rgba(245, 129, 30, 0.7),
                         0 0 30px rgba(245, 129, 30, 0.5);
        }

        .neon-box-strong {
            box-shadow: 0 0 20px rgba(245, 129, 30, 0.8),
                        0 0 40px rgba(245, 129, 30, 0.6),
                        0 0 60px rgba(245, 129, 30, 0.4);
        }

        /* === NUEVOS EFECTOS NEÓN (Concretera gris - Mantaro naranja) === */
        .neon-gray {
            color: #d1d5db;
            text-shadow: 
                0 0 10px #d1d5db,
                0 0 20px #d1d5db,
                0 0 40px #9ca3af,
                0 0 60px #6b7280;
            transition: all 0.3s ease;
        }

        .neon-orange {
            color: #fb923c;
            text-shadow: 
                0 0 10px #fb923c,
                0 0 20px #fb923c,
                0 0 40px #f97316,
                0 0 60px #ea580c;
            transition: all 0.3s ease;
        }

        /* Hover en el logo */
        .group:hover .neon-gray,
        .group:hover .neon-orange {
            filter: brightness(1.2);
        }

        /* Animación de pulso en el header */
        @keyframes pulse-neon {
            0%, 100% { 
                box-shadow: 0 0 8px rgba(245, 129, 30, 0.3),
                           0 4px 20px rgba(0, 0, 0, 0.3);
            }
            50% { 
                box-shadow: 0 0 25px rgba(245, 129, 30, 0.7),
                           0 4px 25px rgba(0, 0, 0, 0.4);
            }
        }
        
        .animate-pulse-neon {
            animation: pulse-neon 3s infinite ease-in-out;
        }

        /* Navegación */
        .nav-link {
            color: var(--color-white);
            margin: 0 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--color-gold);
            text-shadow: 0 0 8px rgba(221, 179, 31, 0.7);
        }

        .hover-neon-glow:hover {
            text-shadow: 0 0 15px rgba(245, 129, 30, 1),
                         0 0 30px rgba(221, 179, 31, 0.8);
        }

        /* Botón primario */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: var(--color-white);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 0 8px rgba(245, 129, 30, 0.4);
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 0 18px rgba(245, 129, 30, 0.8);
        }

        /* Dropdown */
        .dropdown-neon {
            background-color: #1a2632;
            border: 1px solid var(--color-gold);
            border-radius: 0.75rem;
            backdrop-filter: blur(4px);
        }
        
        .dropdown-neon a:hover {
            background-color: #2a3a4a;
            color: var(--color-gold);
        }

        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--color-dark);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--color-primary);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-gold);
        }

        /* ========== EFECTO PRELOADER (CHASQUIDO DE THANOS) ========== */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes thanosSnap {
            0% {
                opacity: 1;
                transform: scale(1);
                filter: blur(0px);
            }
            40% {
                opacity: 0.8;
                transform: scale(0.98);
                filter: blur(2px);
            }
            70% {
                opacity: 0.4;
                transform: scale(0.95);
                filter: blur(5px);
            }
            100% {
                opacity: 0;
                transform: scale(0.9);
                filter: blur(10px);
                visibility: hidden;
                display: none;
            }
        }

        .thanos-fade-out {
            animation: thanosSnap 0.9s ease-out forwards;
        }

        .preloader-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .preloader-content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: rgba(22, 34, 46, 0.5);
        }

        .preloader-logo {
            width: 200px;
            max-width: 60vw;
            height: auto;
            margin-bottom: 1.5rem;
            animation: pulseGlow 1.5s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                filter: drop-shadow(0 0 5px rgba(245, 129, 30, 0.5));
            }
            50% {
                transform: scale(1.03);
                filter: drop-shadow(0 0 20px rgba(245, 129, 30, 0.8));
            }
        }

        .preloader-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-top: 1rem;
        }

        .preloader-title span:first-child {
            color: #d1d5db;
            text-shadow: 0 0 10px #d1d5db, 0 0 20px #9ca3af;
        }

        .preloader-title span:last-child {
            color: #fb923c;
            text-shadow: 0 0 10px #fb923c, 0 0 20px #f97316;
        }

        .preloader-subtitle {
            color: #e5e7eb;
            margin-top: 0.5rem;
            font-size: 1rem;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .preloader-title { 
                font-size: 1.5rem; 
            }
            .preloader-subtitle { 
                font-size: 0.75rem; 
            }
            .preloader-logo {
                width: 120px;
            }
        }
    </style>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <style>
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
    <main class="w-full px-0 py-10">
        <div class="container mx-auto px-4">
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