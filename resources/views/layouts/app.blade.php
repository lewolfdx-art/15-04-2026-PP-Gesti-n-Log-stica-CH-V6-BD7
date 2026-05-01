@php
    $config = App\Models\Inicio::getActiveContent();
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
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
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
            background-color: #ffffff;
            color: #111;
        }

        main {
            flex: 1;
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .btn-rojo {
            background-color: #b40000;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-rojo:hover {
            background-color: #8d0000;
            transform: scale(1.02);
        }

        .btn-azul {
            background-color: #002366;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-azul:hover {
            background-color: #001a4d;
            transform: scale(1.02);
        }

        .nav-link {
            color: #ffffff;
            margin: 0 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #ffcccc;
            text-decoration: underline;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    @yield('styles')
</head>
<body>

<!-- Header -->
<header style="background-color: #002366;" class="py-6 shadow-lg">
    <div class="flex flex-col md:flex-row items-center justify-between px-4">

        <a href="{{ url('/') }}" class="flex items-center gap-3">
            @if($config && $config->logo_image)
                <img src="{{ Storage::url($config->logo_image) }}" alt="Logo" class="w-16 h-16 object-contain">
            @else
                <img src="{{ asset('img/logo1.jpg') }}" alt="Logo" class="w-16 h-16 object-contain">
            @endif
            <div class="text-3xl font-extrabold uppercase tracking-wide logo-title">
                @if($config && $config->logo_text)
                    @php
                        $logoText = $config->logo_text;
                        $firstWord = explode(' ', $logoText)[0];
                        $restWords = substr($logoText, strlen($firstWord));
                    @endphp
                    <span style="color: {{ $config->logo_span_color ?? '#b40000' }};">{{ $firstWord }}</span>
                    <span style="color: {{ $config->logo_text_color ?? '#ffffff' }};">{{ $restWords }}</span>
                @else
                    <span style="color: #b40000;">Concretera</span>
                    <span style="color: #ffffff;">Huancayo</span>
                @endif
            </div>
        </a>

        <!-- Navegación -->
        <nav class="mt-4 md:mt-0 text-center md:text-right text-sm flex flex-wrap justify-center md:justify-end items-center gap-3 md:gap-4">
            <a href="{{ url('/') }}" class="nav-link inline-block">Inicio</a>
            <a href="{{ url('/nosotros') }}" class="nav-link inline-block">Nosotros</a>
            <a href="{{ url('/productos') }}" class="nav-link inline-block">Productos</a>

            <!-- Sostenibilidad desplegable -->
            <div x-data="{ open: false }" class="relative" @mouseleave="open = false">
                <button @mouseenter="open = true" class="nav-link inline-block">Sostenibilidad</button>
                <div x-show="open" x-transition
                    class="absolute mt-2 bg-white border border-gray-200 rounded-lg shadow-lg text-left z-50 w-48"
                    @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ url('/sostenibilidad') }}"
                       class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-red-700 transition">Políticas</a>
                    <a href="{{ url('/experiencia') }}"
                       class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-red-700 transition">Experiencia</a>
                    <a href="{{ url('/concretips') }}"
                       class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-red-700 transition">Tips</a>
                </div>
            </div>

            <a href="{{ url('/contacto') }}" class="nav-link inline-block">Contacto</a>
            <a href="{{ url('/libro-reclamaciones') }}" class="nav-link inline-block">Libro de Reclamaciones</a>

            <!-- Botón Login/Logout -->
            @guest
                <a href="{{ url('/admin/login') }}" class="btn-rojo inline-block text-sm">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            @else
                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="btn-rojo inline-block text-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            @endguest
        </nav>
    </div>
</header>

<!-- Contenido principal -->
<main class="w-full px-0 py-10">
    <div class="container mx-auto px-4">
        @yield('content')
    </div>
</main>

<!-- Footer dinámico -->
<footer class="bg-gray-900 text-white pt-10 pb-6 px-6 mt-10 text-sm">
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Oficina Administrativa -->
        <div>
            <h4 class="text-lg font-bold mb-2 text-yellow-400">{{ $config->oficina_titulo ?? 'Oficina Administrativa' }}</h4>
            <p><strong>Ubicación de oficina:</strong> 
                <a href="{{ $config->oficina_maps_url ?? '#' }}" target="_blank" class="underline text-blue-300">
                    Ver en Google Maps
                </a>
            </p>
            <p><strong>Dirección:</strong> {{ $config->oficina_direccion ?? 'Pasaje Miraflores, 12003 – Huancayo' }}</p>
            <p><strong>Teléfono:</strong> {{ $config->oficina_telefono ?? '064 762805' }}</p>
            <p><strong>Celular:</strong> {{ $config->oficina_celular ?? '925 091 695' }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $config->oficina_email ?? 'amgeotop.sac@gmail.com' }}" class="underline">{{ $config->oficina_email ?? 'amgeotop.sac@gmail.com' }}</a></p>
        </div>

        <!-- Planta de Producción -->
        <div>
            <h4 class="text-lg font-bold mb-2 text-yellow-400">{{ $config->planta_titulo ?? 'Planta de Producción' }}</h4>
            <p><strong>Ubicación de planta:</strong> 
                <a href="{{ $config->planta_maps_url ?? '#' }}" target="_blank" class="underline text-blue-300">
                    Ver en Google Maps
                </a>
            </p>
            <p><strong>WhatsApp:</strong> {{ $config->planta_whatsapp ?? '+51 982 337 770' }}</p>
        </div>

        <!-- Atención al Cliente -->
        <div>
            <h4 class="text-lg font-bold mb-2 text-yellow-400">{{ $config->atencion_titulo ?? 'Atención al Cliente' }}</h4>
            <p>Lunes a Sábado: {{ $config->atencion_horario_lunes_sabado ?? '8:00 a.m. – 6:00 p.m.' }}</p>
            <p>Domingos: {{ $config->atencion_horario_domingo ?? 'Atención previa coordinación' }}</p>
            <p><strong>Asesora Comercial:</strong> {{ $config->atencion_asesora_comercial ?? '982 337 770' }}</p>
        </div>
    </div>

    <!-- Redes Sociales -->
    @if($config && $config->social_networks)
    <div class="flex justify-center gap-6 mt-6">
        @foreach($config->social_networks as $social)
            <a href="{{ $social['url'] }}" target="_blank" class="text-gray-400 hover:text-yellow-400 transition">
                <i class="{{ $social['icon'] }} text-2xl"></i>
            </a>
        @endforeach
    </div>
    @endif

    <div class="text-center mt-8 border-t pt-4 border-gray-700">
        © {{ date('Y') }} Concretera Mantaro. Todos los derechos reservados.
    </div>
</footer>

@yield('scripts')
</body>
</html>