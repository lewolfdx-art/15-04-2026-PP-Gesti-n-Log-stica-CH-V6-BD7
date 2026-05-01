@extends('layouts.app')

@section('title', 'Inicio - Concretera Mantaro')

@section('content')
    @php
        $inicio = App\Models\Inicio::getActiveContent();
        
        $beneficios = [];
        $proyectos = [];
        $operaciones = [];
        
        if ($inicio) {
            if ($inicio->beneficios) {
                $beneficios = is_array($inicio->beneficios) ? $inicio->beneficios : json_decode($inicio->beneficios, true);
            }
            if ($inicio->proyectos_recientes) {
                $proyectos = is_array($inicio->proyectos_recientes) ? $inicio->proyectos_recientes : json_decode($inicio->proyectos_recientes, true);
            }
            if ($inicio->nuestras_operaciones) {
                $operaciones = is_array($inicio->nuestras_operaciones) ? $inicio->nuestras_operaciones : json_decode($inicio->nuestras_operaciones, true);
            }
        }
    @endphp

    <style>
        /* Nuevos colores corporativos */
        :root {
            --color-primary: #f5811e;      /* Naranja principal */
            --color-primary-dark: #e06e0a;
            --color-dark: #16222e;         /* Azul oscuro profundo */
            --color-gold: #ddb31f;         /* Amarillo dorado */
            --color-terracota: #da914f;    /* Ocre / Terracota */
            --color-gray-mid: #808185;     /* Gris medio */
            --color-white: #fcfcfc;
        }

        .hero-custom {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .hero-custom h1 {
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .hero-custom h1 span {
            color: var(--color-primary);
        }
        .hero-custom p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 1.5rem auto;
            color: #e2e2e2;
        }
        .btn-cotizar {
            background-color: var(--color-primary);
            color: var(--color-white);
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-cotizar:hover {
            background-color: var(--color-primary-dark);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(245, 129, 30, 0.4);
        }
        .beneficios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .beneficio-card-custom {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-top: 4px solid var(--color-primary);
        }
        .beneficio-card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .beneficio-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem auto;
            background: linear-gradient(135deg, #fff5eb, #ffe8d6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .beneficio-icon i {
            font-size: 2.5rem;
            color: var(--color-primary);
        }
        .beneficio-card-custom h3 {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--color-dark);
            margin-bottom: 0.75rem;
        }
        .beneficio-card-custom p {
            color: #4b5563;
        }
        .section-title {
            font-size: 2rem;
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
        .operaciones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .operacion-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-bottom: 3px solid var(--color-gold);
        }
        .operacion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .operacion-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .operacion-card .info {
            padding: 1.25rem;
        }
        .operacion-card h3 {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .operacion-card p {
            color: #4b5563;
            font-size: 0.9rem;
        }
        .proyectos-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #eef2f5 100%);
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            margin-bottom: 3rem;
        }
        .proyectos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .proyecto-card {
            background: white;
            border-left: 8px solid var(--color-primary);
            padding: 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .proyecto-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .proyecto-card h3 {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .proyecto-card p {
            color: #4b5563;
        }
        /* WhatsApp Float Button */
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
        }
        @media (max-width: 768px) {
            .hero-custom h1 { font-size: 1.8rem; }
            .operaciones-grid { grid-template-columns: 1fr; }
            .beneficios-grid { grid-template-columns: 1fr; }
            .proyectos-grid { grid-template-columns: 1fr; }
            .whatsapp-float {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
                bottom: 20px;
                right: 20px;
            }
            .section-title { font-size: 1.5rem; }
        }
    </style>

    @if($inicio)
        <!-- Hero principal -->
        <div class="hero-custom">
            <h1>{{ $inicio->hero_title ?? 'Concreto <span>Premezclado</span> de Alta Calidad' }}</h1>
            <p>{{ $inicio->hero_description ?? 'Para obras y proyectos en Huancayo y toda la región' }}</p>
            @if($inicio->cta_text && $inicio->cta_url)
                <a href="{{ $inicio->cta_url }}" class="btn-cotizar">
                    {{ $inicio->cta_text }}
                </a>
            @endif
        </div>

        <!-- Proyectos Recientes -->
        @if(!empty($proyectos) && count($proyectos) > 0)
        <div class="proyectos-section">
            <h2 class="section-title">Proyectos <span>Recientes</span></h2>
            <div class="proyectos-grid">
                @foreach($proyectos as $proyecto)
                    <div class="proyecto-card">
                        @if(!empty($proyecto['image']))
                            <img src="{{ Storage::url($proyecto['image']) }}" 
                                 alt="{{ $proyecto['title'] }}"
                                 class="w-full h-48 object-cover rounded-lg mb-3">
                        @else
                            <img src="{{ asset('img/placeholder.jpg') }}" 
                                 alt="Sin imagen"
                                 class="w-full h-48 object-cover rounded-lg mb-3">
                        @endif
                        <h3>{{ $proyecto['title'] }}</h3>
                        <p>{{ $proyecto['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Beneficios -->
        @if(!empty($beneficios) && count($beneficios) > 0)
            <section>
                <h2 class="section-title">Nuestros <span>Beneficios</span></h2>
                <div class="beneficios-grid">
                    @foreach($beneficios as $beneficio)
                        <div class="beneficio-card-custom">
                            <div class="beneficio-icon">
                                <i class="{{ $beneficio['icon'] ?? 'bi-star-fill' }}"></i>
                            </div>
                            <h3>{{ $beneficio['title'] }}</h3>
                            <p>{{ $beneficio['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Nuestras Operaciones -->
        @if(!empty($operaciones) && count($operaciones) > 0)
            <section>
                <h2 class="section-title">Nuestras <span>Operaciones</span></h2>
                <div class="operaciones-grid">
                    @foreach($operaciones as $operacion)
                        <div class="operacion-card">
                            @if(!empty($operacion['image']))
                                <img src="{{ Storage::url($operacion['image']) }}" alt="{{ $operacion['title'] }}">
                            @else
                                <img src="{{ asset('img/placeholder.jpg') }}" alt="Imagen no disponible">
                            @endif
                            <div class="info">
                                <h3>{{ $operacion['title'] }}</h3>
                                <p>{{ $operacion['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Botón WhatsApp Flotante -->
        @if($inicio->whatsapp_number)
            <a href="https://wa.me/{{ $inicio->whatsapp_number }}" 
               class="whatsapp-float" 
               target="_blank"
               title="Chatea con nosotros">
                <i class="bi bi-whatsapp"></i>
            </a>
        @endif

    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-gear" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">Configuración pendiente</h2>
            <p style="color: #6b7280;">Por favor, ingresa al panel de administración para configurar el contenido de inicio.</p>
            <a href="{{ url('/admin/inicios/create') }}" class="btn-cotizar" style="background-color: var(--color-primary);">Ir al Panel</a>
        </div>
    @endif
@endsection