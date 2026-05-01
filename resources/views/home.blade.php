@extends('layouts.app')

@section('title', 'Inicio - Concretera Huancayo')

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
            if ($inicio->proyectos_destacados) {
                $proyectos = is_array($inicio->proyectos_destacados) ? $inicio->proyectos_destacados : json_decode($inicio->proyectos_destacados, true);
            }
            if ($inicio->nuestras_operaciones) {
                $operaciones = is_array($inicio->nuestras_operaciones) ? $inicio->nuestras_operaciones : json_decode($inicio->nuestras_operaciones, true);
            }
        }
    @endphp

    <style>
        .hero-custom {
            background: linear-gradient(135deg, #002366 0%, #001a4d 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: white;
        }
        .hero-custom h1 {
            font-size: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .hero-custom p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 1.5rem auto;
        }
        .btn-cotizar {
            background-color: #b40000;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-cotizar:hover {
            background-color: #8d0000;
            transform: scale(1.05);
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
        }
        .beneficio-card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .beneficio-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem auto;
            background-color: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .beneficio-icon i {
            font-size: 2.5rem;
            color: #b40000;
        }
        .beneficio-card-custom h3 {
            font-size: 1.25rem;
            font-weight: bold;
            color: #002366;
            margin-bottom: 0.75rem;
        }
        .section-title {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            color: #002366;
            margin-bottom: 2rem;
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
            color: #b40000;
            margin-bottom: 0.5rem;
        }
        .operacion-card p {
            color: #4b5563;
            font-size: 0.9rem;
        }
        .proyectos-section {
            background-color: #f9fafb;
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
            border-left: 8px solid #b40000;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }
        .proyecto-card h3 {
            font-size: 1.2rem;
            font-weight: bold;
            color: #b40000;
            margin-bottom: 0.5rem;
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
        }
    </style>

    @if($inicio)
        <!-- Hero principal -->
        <div class="hero-custom">
            <h1>{{ $inicio->hero_title ?? 'Concreto Premezclado de Alta Calidad' }}</h1>
            <p>{{ $inicio->hero_description ?? '' }}</p>
            @if($inicio->cta_text && $inicio->cta_url)
                <a href="{{ $inicio->cta_url }}" class="btn-cotizar">
                    {{ $inicio->cta_text }}
                </a>
            @endif
        </div>

        <!-- Beneficios -->
        @if(!empty($beneficios) && count($beneficios) > 0)
            <section>
                <h2 class="section-title">Nuestros Beneficios</h2>
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
                <h2 class="section-title">Nuestras Operaciones</h2>
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

        <!-- Proyectos Destacados -->
        @if(!empty($proyectos) && count($proyectos) > 0)
            <div class="proyectos-section">
                <h2 class="section-title">Proyectos Recientes</h2>
                <div class="proyectos-grid">
                    @foreach($proyectos as $proyecto)
                        <div class="proyecto-card">
                            <h3>{{ $proyecto['title'] }}</h3>
                            <p>{{ $proyecto['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
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
            <a href="{{ url('/admin/inicios/create') }}" class="btn-cotizar" style="background-color: #2563eb;">Ir al Panel</a>
        </div>
    @endif
@endsection