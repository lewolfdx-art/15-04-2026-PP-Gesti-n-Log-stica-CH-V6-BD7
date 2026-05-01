@extends('layouts.app')

@section('title', 'Concretips - Concretera Mantaro')

@section('content')
    @php
        $sostenibilidad = App\Models\Sostenibilidad::getActiveContent();
        $recomendaciones = [];
        
        if ($sostenibilidad && $sostenibilidad->recomendaciones_tecnicas) {
            $recomendaciones = is_array($sostenibilidad->recomendaciones_tecnicas) 
                ? $sostenibilidad->recomendaciones_tecnicas 
                : json_decode($sostenibilidad->recomendaciones_tecnicas, true);
        }
    @endphp

    <style>
        .hero-tips {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .hero-tips h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .hero-tips h1 span {
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
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .tip-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .tip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .tip-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .tip-card .content {
            padding: 1.5rem;
        }
        .tip-card h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 0.75rem;
        }
        .tip-card .description {
            color: #4b5563;
            line-height: 1.6;
        }
        .tip-card ul {
            margin-top: 0.75rem;
            padding-left: 1.2rem;
        }
        .tip-card li {
            margin: 0.25rem 0;
        }
        @media (max-width: 768px) {
            .hero-tips h1 { font-size: 1.8rem; }
            .tips-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="hero-tips">
        <h1>RECOMENDACIONES TÉCNICAS PARA <span>CONSTRUCCIÓN</span></h1>
        <p>Consejos y buenas prácticas para el uso óptimo del concreto premezclado en tus proyectos.</p>
    </div>

    @if(!empty($recomendaciones))
        <div class="tips-grid">
            @foreach($recomendaciones as $recomendacion)
                <div class="tip-card">
                    @if(!empty($recomendacion['image']))
                        <img src="{{ Storage::url($recomendacion['image']) }}" alt="{{ $recomendacion['title'] }}">
                    @else
                        <img src="{{ asset('img/placeholder-tip.jpg') }}" alt="{{ $recomendacion['title'] }}">
                    @endif
                    <div class="content">
                        <h3>{{ $recomendacion['title'] }}</h3>
                        <div class="description">{!! $recomendacion['description'] !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-lightbulb" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">No hay recomendaciones disponibles</h2>
            <p style="color: #6b7280;">Pronto agregaremos consejos técnicos para construcción.</p>
        </div>
    @endif
@endsection