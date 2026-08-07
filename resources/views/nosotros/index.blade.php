@extends('layouts.app')

@section('title', 'Nosotros - Concretera Mantaro')

@section('content')
    @php
        $nosotros = App\Models\Nosotros::getActiveContent();
        
        $valoresList = [];
        
        if ($nosotros && $nosotros->valores_list) {
            $valoresList = is_array($nosotros->valores_list) 
                ? $nosotros->valores_list 
                : json_decode($nosotros->valores_list, true);
        }
    @endphp

    <style>
        .section-block {
            margin-bottom: 3rem;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .section-header {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            padding: 1.5rem 2rem;
            border-bottom: 3px solid var(--color-primary);
        }
        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin: 0;
        }
        .section-header h2 span {
            color: var(--color-primary);
        }
        .section-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem;
        }
        .section-text {
            color: #4b5563;
            line-height: 1.6;
        }
        .section-text h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }
        .section-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 0.75rem;
        }
        .section-image-full {
            padding: 0 2rem 2rem 2rem;
        }
        .section-image-full img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 0.75rem;
        }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
        }
        .value-item {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border-left: 4px solid var(--color-primary);
        }
        .value-item h4 {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .value-item p {
            color: #4b5563;
            font-size: 0.9rem;
            margin: 0;
        }
        @media (max-width: 768px) {
            .section-content {
                grid-template-columns: 1fr;
            }
            .section-header h2 { font-size: 1.4rem; }
        }
    </style>

    @if($nosotros)
        
        <!-- ===================== QUIÉNES SOMOS ===================== -->
        @if($nosotros->quienes_somos_title || $nosotros->quienes_somos_text)
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->quienes_somos_title ?? 'Quiénes <span>Somos</span>' }}</h2>
            </div>
            <div class="section-content">
                <div class="section-text">
                    {!! $nosotros->quienes_somos_text ?? 'Somos una empresa peruana especializada en la producción de concreto premezclado...' !!}
                </div>
                @if($nosotros->quienes_somos_image)
                <div class="section-image">
                    <img src="{{ Storage::url($nosotros->quienes_somos_image) }}" alt="Quiénes Somos">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- ===================== MISIÓN ===================== -->
        @if($nosotros->mision_title || $nosotros->mision_text)
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->mision_title ?? 'Misión' }}</h2>
            </div>
            <div class="section-content">
                <div class="section-text">
                    {!! $nosotros->mision_text !!}
                </div>
                @if($nosotros->mision_image)
                <div class="section-image">
                    <img src="{{ Storage::url($nosotros->mision_image) }}" alt="Misión">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- ===================== VISIÓN ===================== -->
        @if($nosotros->vision_title || $nosotros->vision_text)
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->vision_title ?? 'Visión' }}</h2>
            </div>
            <div class="section-content">
                <div class="section-text">
                    {!! $nosotros->vision_text !!}
                </div>
                @if($nosotros->vision_image)
                <div class="section-image">
                    <img src="{{ Storage::url($nosotros->vision_image) }}" alt="Visión">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- ===================== VALORES ===================== -->
        @if($nosotros->valores_title || !empty($valoresList))
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->valores_title ?? 'Nuestros <span>Valores</span>' }}</h2>
            </div>
            @if($nosotros->valores_image)
            <div class="section-image-full">
                <img src="{{ Storage::url($nosotros->valores_image) }}" alt="Nuestros Valores">
            </div>
            @endif
            @if(!empty($valoresList))
            <div class="values-grid">
                @foreach($valoresList as $valor)
                    <div class="value-item">
                        <h4>{{ $valor['title'] ?? '' }}</h4>
                        <p>{{ $valor['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- ===================== ACTIVIDAD PRINCIPAL ===================== -->
        @if($nosotros->actividad_title || $nosotros->actividad_text)
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->actividad_title ?? 'Actividad <span>Principal</span>' }}</h2>
            </div>
            <div class="section-content">
                <div class="section-text">
                    {!! $nosotros->actividad_text !!}
                </div>
                @if($nosotros->actividad_image)
                <div class="section-image">
                    <img src="{{ Storage::url($nosotros->actividad_image) }}" alt="Actividad Principal">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- ===================== ORGANIGRAMA ===================== -->
        @if($nosotros->organigrama_title || $nosotros->organigrama_text)
        <div class="section-block">
            <div class="section-header">
                <h2>{{ $nosotros->organigrama_title ?? 'Nuestro <span>Organigrama</span>' }}</h2>
            </div>
            <div class="section-content">
                <div class="section-text">
                    {!! $nosotros->organigrama_text !!}
                </div>
                @if($nosotros->organigrama_image)
                <div class="section-image">
                    <img src="{{ Storage::url($nosotros->organigrama_image) }}" alt="Organigrama">
                </div>
                @endif
            </div>
        </div>
        @endif

    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-gear" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">Configuración pendiente</h2>
            <p style="color: #6b7280;">Por favor, ingresa al panel de administración para configurar el contenido de Nosotros.</p>
            <a href="{{ url('/admin/nosotros/create') }}" class="btn-cotizar" style="background-color: var(--color-primary);">Ir al Panel</a>
        </div>
    @endif
@endsection