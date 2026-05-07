@php
    $config = App\Models\Inicio::getActiveContent();
    $isHomePage = request()->path() === '/';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Concretera Huancayo')</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

        /* === NUEVOS EFECTOS NEÓN (Concretera azul - Mantaro naranja) === */
        .neon-blue {
            color: #67e8f9;
            text-shadow: 
                0 0 10px #67e8f9,
                0 0 20px #67e8f9,
                0 0 40px #22d3ee,
                0 0 60px #06b6d4;
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
        .group:hover .neon-blue,
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
            color: #67e8f9;
            text-shadow: 0 0 10px #67e8f9, 0 0 20px #22d3ee;
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

    @yield('styles')
</head>
<body>

<!-- PANTALLA DE CARGA - EFECTO CHASQUIDO DE THANOS -->
{{-- SOLO SE MUESTRA EN LA PÁGINA DE INICIO --}}
@if($isHomePage)
<div id="preloader">
    <!-- Imagen de fondo - se ve completa -->
    <img src="{{ asset('img/preloader-logo.png') }}" 
         alt="Fondo Concretera Mantaro" 
         class="preloader-background"
         onerror="this.style.display='none'">
    
    <!-- Texto sobre la imagen -->
    <div class="preloader-content">
        <div class="preloader-title">
            <span>Concretera</span>
            <span> Mantaro</span>
        </div>
        <div class="preloader-subtitle">
            <i class="fas fa-hard-hat"></i> Construyendo futuro con concreto de alta calidad
        </div>
    </div>
</div>
@endif

<!-- HEADER CON EFECTO NEÓN -->
<header style="background-color: #16222e;" class="fixed w-full top-0 z-50 py-4 shadow-2xl animate-pulse-neon border-b border-[#f5811e]/30">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Logo + marca con neón azul y naranja -->
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            @if($config && $config->logo_image)
                <img src="{{ Storage::url($config->logo_image) }}" alt="Logo" class="w-14 h-14 object-contain rounded-lg">
            @else
                <div class="bg-gradient-to-br from-[#f5811e] to-[#e06e0a] rounded-xl p-2 neon-box-strong">
                    <i class="fas fa-hard-hat text-2xl text-white"></i>
                </div>
            @endif
            
            <div class="text-4xl md:text-5xl font-extrabold tracking-tighter">
                @if($config && $config->logo_text && trim($config->logo_text) != '')
                    @php
                        $logoText = trim($config->logo_text);
                        $parts = explode(' ', $logoText, 2);
                        $firstWord = $parts[0] ?? 'Concretera';
                        $secondWord = $parts[1] ?? 'Mantaro';
                    @endphp
                    <span class="neon-blue">{{ $firstWord }}</span>
                    <span class="neon-orange">{{ $secondWord }}</span>
                @else
                    <span class="neon-blue">Concretera</span>
                    <span class="neon-orange">Mantaro</span>
                @endif
            </div>
        </a>

        <!-- Navegación -->
        <nav class="flex flex-wrap justify-center items-center gap-2 md:gap-4">
            <a href="{{ url('/') }}" class="nav-link text-base hover-neon-glow">Inicio</a>
            <a href="{{ url('/nosotros') }}" class="nav-link text-base hover-neon-glow">Nosotros</a>
            <a href="{{ url('/productos') }}" class="nav-link text-base hover-neon-glow">Productos</a>

            <div x-data="{ open: false }" class="relative" @mouseleave="open = false">
                <button @mouseenter="open = true" class="nav-link text-base hover-neon-glow flex items-center gap-1">
                    Sostenibilidad <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" x-transition.opacity.duration.200ms
                     class="absolute left-0 mt-2 w-48 dropdown-neon shadow-xl z-50 py-2"
                     @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ url('/sostenibilidad') }}" class="block px-4 py-2 text-white hover:text-[#ddb31f]">🌱 Políticas</a>
                    <a href="{{ url('/experiencia') }}" class="block px-4 py-2 text-white hover:text-[#ddb31f]">🏭 Experiencia</a>
                    <a href="{{ url('/concretips') }}" class="block px-4 py-2 text-white hover:text-[#ddb31f]">💡 Tips</a>
                </div>
            </div>

            <a href="{{ url('/contacto') }}" class="nav-link text-base hover-neon-glow">Contacto</a>
            <a href="{{ url('/libro-reclamaciones') }}" class="nav-link text-base hover-neon-glow"> Reclamaciones</a>

            @guest
                <a href="{{ url('/admin/login') }}" class="btn-primary text-sm ml-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            @else
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary text-sm ml-2">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            @endguest
        </nav>
    </div>
</header>

<!-- Espacio para header fijo -->
<div class="pt-28"></div>

<!-- Contenido principal -->
<main class="w-full px-0 py-10">
    <div class="container mx-auto px-4">
        @yield('content')
    </div>
</main>

<!-- Footer -->
<footer style="background-color: #16222e;" class="text-white pt-10 pb-6 px-6 mt-10 text-sm">
    <div class="grid md:grid-cols-3 gap-6">
        <div>
            <h4 class="text-lg font-bold mb-2" style="color: #ddb31f;">{{ $config->oficina_titulo ?? 'Oficina Administrativa' }}</h4>
            <p><strong>Ubicación de oficina:</strong> 
                <a href="{{ $config->oficina_maps_url ?? '#' }}" target="_blank" class="underline" style="color: #da914f;">Ver en Google Maps</a>
            </p>
            <p><strong>Dirección:</strong> {{ $config->oficina_direccion ?? 'Pasaje Miraflores, 12003 – Huancayo' }}</p>
            <p><strong>Teléfono:</strong> {{ $config->oficina_telefono ?? '064 762805' }}</p>
            <p><strong>Celular:</strong> {{ $config->oficina_celular ?? '925 091 695' }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $config->oficina_email ?? 'amgeotop.sac@gmail.com' }}" class="underline" style="color: #da914f;">{{ $config->oficina_email ?? 'amgeotop.sac@gmail.com' }}</a></p>
        </div>

        <div>
            <h4 class="text-lg font-bold mb-2" style="color: #ddb31f;">{{ $config->planta_titulo ?? 'Planta de Producción' }}</h4>
            <p><strong>Ubicación de planta:</strong> 
                <a href="{{ $config->planta_maps_url ?? '#' }}" target="_blank" class="underline" style="color: #da914f;">Ver en Google Maps</a>
            </p>
            <p><strong>WhatsApp:</strong> {{ $config->planta_whatsapp ?? '+51 982 337 770' }}</p>
        </div>

        <div>
            <h4 class="text-lg font-bold mb-2" style="color: #ddb31f;">{{ $config->atencion_titulo ?? 'Atención al Cliente' }}</h4>
            <p>Lunes a Sábado: {{ $config->atencion_horario_lunes_sabado ?? '8:00 a.m. – 6:00 p.m.' }}</p>
            <p>Domingos: {{ $config->atencion_horario_domingo ?? 'Atención previa coordinación' }}</p>
            <p><strong>Asesora Comercial:</strong> {{ $config->atencion_asesora_comercial ?? '982 337 770' }}</p>
        </div>
    </div>

    @if($config && $config->social_networks)
    <div class="flex justify-center gap-6 mt-6">
        @foreach($config->social_networks as $social)
            <a href="{{ $social['url'] }}" target="_blank" class="transition hover:scale-110" 
               style="color: #808185;" 
               onmouseover="this.style.color='#ddb31f'" 
               onmouseout="this.style.color='#808185'">
                <i class="{{ $social['icon'] }} text-2xl"></i>
            </a>
        @endforeach
    </div>
    @endif

    <div class="text-center mt-8 border-t pt-4" style="border-color: #80818530;">
        <p>© {{ date('Y') }} <span style="color: #f5811e; font-weight: bold;">Concretera Mantaro</span> — 
           <span style="color: #ddb31f;">Construyendo futuro con concreto de alta calidad</span></p>
        <p class="text-xs mt-2 flex items-center justify-center gap-2" style="color: #808185;">
            <i class="fas fa-shield-alt" style="color: #f5811e;"></i> 
            "TU PROYECTO, NUESTRO LEGADO"
            <i class="fas fa-hard-hat" style="color: #f5811e;"></i>
        </p>
    </div>
</footer>

<script>
    // EFECTO CHASQUIDO DE THANOS - SOLO EN PÁGINA DE INICIO
    @if($isHomePage)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.classList.add('thanos-fade-out');
                setTimeout(function() {
                    if (preloader && preloader.parentNode) {
                        preloader.style.display = 'none';
                    }
                }, 900);
            }
        }, 2000);
    });
    @endif
</script>

@yield('scripts')
</body>
</html>