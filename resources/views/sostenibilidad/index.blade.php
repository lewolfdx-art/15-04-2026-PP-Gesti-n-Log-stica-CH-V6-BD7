@extends('layouts.app')

@section('title', 'Sostenibilidad - Concretera Mantaro')

@section('content')
    @php
        $sostenibilidad = App\Models\Sostenibilidad::getActiveContent();
        
        $politicaList = $sostenibilidad ? ($sostenibilidad->politica_ambiental_list ?? []) : [];
        $seguridadList = $sostenibilidad ? ($sostenibilidad->seguridad_salud_list ?? []) : [];
        $responsabilidadList = $sostenibilidad ? ($sostenibilidad->responsabilidad_social_list ?? []) : [];
        $eppList = $sostenibilidad ? ($sostenibilidad->epp_list ?? []) : [];
        $protocolosList = $sostenibilidad ? ($sostenibilidad->protocolos_list ?? []) : [];
        $submodulosList = $sostenibilidad ? ($sostenibilidad->submodulos_list ?? []) : [];
        $recursosAcademicos = $sostenibilidad ? ($sostenibilidad->recursos_academicos ?? []) : [];
        $proyectos = $sostenibilidad ? ($sostenibilidad->proyectos_realizados ?? []) : [];
        $recomendaciones = $sostenibilidad ? ($sostenibilidad->recomendaciones_tecnicas ?? []) : [];
    @endphp

    <style>
        .hero-sostenibilidad {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .hero-sostenibilidad h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .hero-sostenibilidad h1 span {
            color: var(--color-primary);
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        .section-title span {
            color: var(--color-primary);
        }
        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--color-primary);
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        .tres-bloques {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .bloque-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .bloque-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .bloque-card .content {
            padding: 1.5rem;
        }
        .bloque-card h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        .bloque-card ul {
            list-style: none;
            padding: 0;
        }
        .bloque-card li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }
        .bloque-card li::before {
            content: "✓";
            color: var(--color-primary);
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        .grid-2cols {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .lista-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .lista-card h3 {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 1rem;
            text-align: center;
        }
        .lista-card ul {
            list-style: none;
            padding: 0;
        }
        .lista-card li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }
        .lista-card li::before {
            content: "•";
            color: var(--color-primary);
            position: absolute;
            left: 0;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .recursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        .recurso-item {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
            border-left: 4px solid var(--color-primary);
        }
        .proyectos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 768px) {
            .hero-sostenibilidad h1 { font-size: 1.8rem; }
            .section-title { font-size: 1.4rem; }
        }
    </style>

    @if($sostenibilidad)
        <!-- Hero -->
        <div class="hero-sostenibilidad">
            <h1>{{ $sostenibilidad->hero_title ?? 'COMPROMISO CON LA <span>SOSTENIBILIDAD</span>' }}</h1>
            @if($sostenibilidad->hero_image)
                <img src="{{ Storage::url($sostenibilidad->hero_image) }}" alt="Sostenibilidad" class="rounded-lg mb-4 max-h-96 w-full object-cover">
            @endif
            <div>{!! $sostenibilidad->hero_description !!}</div>
        </div>

        <!-- Tres Bloques Temáticos -->
        <div class="tres-bloques">
            <!-- Política Ambiental -->
            <div class="bloque-card">
                @if($sostenibilidad->politica_ambiental_image)
                    <img src="{{ Storage::url($sostenibilidad->politica_ambiental_image) }}" alt="Política Ambiental">
                @endif
                <div class="content">
                    <h3>{{ $sostenibilidad->politica_ambiental_title ?? 'Política Ambiental' }}</h3>
                    <ul>
                        @foreach($politicaList as $item)
                            <li>{{ $item['item'] ?? $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Seguridad y Salud -->
            <div class="bloque-card">
                @if($sostenibilidad->seguridad_salud_image)
                    <img src="{{ Storage::url($sostenibilidad->seguridad_salud_image) }}" alt="Seguridad y Salud">
                @endif
                <div class="content">
                    <h3>{{ $sostenibilidad->seguridad_salud_title ?? 'Seguridad y Salud Ocupacional' }}</h3>
                    <ul>
                        @foreach($seguridadList as $item)
                            <li>{{ $item['item'] ?? $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Responsabilidad Social -->
            <div class="bloque-card">
                @if($sostenibilidad->responsabilidad_social_image)
                    <img src="{{ Storage::url($sostenibilidad->responsabilidad_social_image) }}" alt="Responsabilidad Social">
                @endif
                <div class="content">
                    <h3>{{ $sostenibilidad->responsabilidad_social_title ?? 'Responsabilidad Social' }}</h3>
                    <ul>
                        @foreach($responsabilidadList as $item)
                            <li>{{ $item['item'] ?? $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Seguridad en el Trabajo -->
        <h2 class="section-title">SEGURIDAD EN EL <span>TRABAJO</span></h2>
        <div class="grid-2cols">
            <div class="lista-card">
                <h3>Equipos de Protección Personal (EPP)</h3>
                <ul>
                    @foreach($eppList as $item)
                        <li>{{ $item['item'] ?? $item }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="lista-card">
                <h3>Otros Protocolos</h3>
                <ul>
                    @foreach($protocolosList as $item)
                        <li>{{ $item['item'] ?? $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Submódulos ERP -->
        <h2 class="section-title">SUBMÓDULOS DE GESTIÓN DE <span>SEGURIDAD (ERP)</span></h2>
        <div class="grid-2cols">
            @foreach($submodulosList as $item)
                <div class="lista-card">
                    <h3>{{ $item['item'] ?? $item }}</h3>
                </div>
            @endforeach
        </div>

        <!-- Recursos Académicos y Normativos -->
        <h2 class="section-title">RECURSOS ACADÉMICOS Y <span>NORMATIVOS</span></h2>
        <div class="recursos-grid">
            @foreach($recursosAcademicos as $recurso)
                <div class="recurso-item">
                    <strong>{{ $recurso['title'] ?? '' }}</strong><br>
                    <small>Fuente: {{ $recurso['source'] ?? '' }}</small>
                    @if(!empty($recurso['link']))
                        <br><a href="{{ $recurso['link'] }}" target="_blank" class="text-[#f5811e] text-sm">Ver documento →</a>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Proyectos Realizados -->
        @if(!empty($proyectos))
            <h2 class="section-title">PROYECTOS <span>REALIZADOS</span></h2>
            <div class="proyectos-grid">
                @foreach($proyectos as $proyecto)
                    <div class="bloque-card">
                        @if(!empty($proyecto['image']))
                            <img src="{{ Storage::url($proyecto['image']) }}" alt="{{ $proyecto['title'] }}">
                        @endif
                        <div class="content">
                            <h3>{{ $proyecto['title'] }}</h3>
                            <p>{{ $proyecto['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Recomendaciones Técnicas -->
        @if(!empty($recomendaciones))
            <h2 class="section-title">RECOMENDACIONES TÉCNICAS PARA <span>CONSTRUCCIÓN</span></h2>
            @foreach($recomendaciones as $recomendacion)
                <div class="bloque-card" style="margin-bottom: 1.5rem;">
                    @if(!empty($recomendacion['image']))
                        <img src="{{ Storage::url($recomendacion['image']) }}" alt="{{ $recomendacion['title'] }}">
                    @endif
                    <div class="content">
                        <h3>{{ $recomendacion['title'] }}</h3>
                        <div>{!! $recomendacion['description'] !!}</div>
                    </div>
                </div>
            @endforeach
        @endif

    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-gear" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">Configuración pendiente</h2>
            <p style="color: #6b7280;">Por favor, ingresa al panel de administración para configurar el contenido de Sostenibilidad.</p>
            <a href="{{ url('/admin/sostenibilidad/create') }}" class="btn-cotizar" style="background-color: var(--color-primary);">Ir al Panel</a>
        </div>
    @endif
@endsection