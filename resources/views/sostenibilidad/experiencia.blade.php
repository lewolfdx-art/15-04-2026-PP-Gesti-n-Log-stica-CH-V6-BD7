@extends('layouts.app')

@section('title', 'Experiencia - Concretera Mantaro')

@section('content')
    @php
        $sostenibilidad = App\Models\Sostenibilidad::getActiveContent();
        $proyectos = [];
        
        if ($sostenibilidad && $sostenibilidad->proyectos_realizados) {
            $proyectos = is_array($sostenibilidad->proyectos_realizados) 
                ? $sostenibilidad->proyectos_realizados 
                : json_decode($sostenibilidad->proyectos_realizados, true);
        }
    @endphp

    <style>
        .hero-experiencia {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .hero-experiencia h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .hero-experiencia h1 span {
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
        .proyectos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .proyecto-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .proyecto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .proyecto-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .proyecto-card .content {
            padding: 1.5rem;
        }
        .proyecto-card h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .proyecto-card p {
            color: #4b5563;
        }
        @media (max-width: 768px) {
            .hero-experiencia h1 { font-size: 1.8rem; }
            .proyectos-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="hero-experiencia">
        <h1>PROYECTOS <span>REALIZADOS</span></h1>
        <p>Conoce los proyectos más importantes que hemos desarrollado con concreto de alta calidad.</p>
    </div>

    @if(!empty($proyectos))
        <div class="proyectos-grid">
            @foreach($proyectos as $proyecto)
                <div class="proyecto-card">
                    @if(!empty($proyecto['image']))
                        <img src="{{ Storage::url($proyecto['image']) }}" alt="{{ $proyecto['title'] }}">
                    @else
                        <img src="{{ asset('img/placeholder-proyecto.jpg') }}" alt="{{ $proyecto['title'] }}">
                    @endif
                    <div class="content">
                        <h3>{{ $proyecto['title'] }}</h3>
                        <p>{{ $proyecto['description'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-building" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">No hay proyectos disponibles</h2>
            <p style="color: #6b7280;">Pronto agregaremos nuestros proyectos realizados.</p>
        </div>
    @endif
@endsection