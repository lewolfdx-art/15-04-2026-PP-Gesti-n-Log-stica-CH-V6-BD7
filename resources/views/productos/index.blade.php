@extends('layouts.app')

@section('title', 'Productos - Concretera Mantaro')

@section('content')
    @php
        $productos = App\Models\Producto::getActiveContent();
        
        $tiposConcreto = [];
        $servicios = [];
        
        if ($productos) {
            if ($productos->tipos_concreto) {
                $tiposConcreto = is_array($productos->tipos_concreto) 
                    ? $productos->tipos_concreto 
                    : json_decode($productos->tipos_concreto, true);
            }
            if ($productos->servicios_complementarios) {
                $servicios = is_array($productos->servicios_complementarios) 
                    ? $productos->servicios_complementarios 
                    : json_decode($productos->servicios_complementarios, true);
            }
        }
    @endphp

    <style>
        .hero-products {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .hero-products h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .hero-products h1 span {
            color: var(--color-primary);
        }
        .hero-products p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
            color: #e2e2e2;
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
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .producto-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #eef2f5;
        }
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .producto-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .producto-info {
            padding: 1.5rem;
        }
        .producto-info h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--color-dark);
            margin-bottom: 0.75rem;
        }
        .producto-info .description {
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        @media (max-width: 768px) {
            .hero-products h1 { font-size: 1.8rem; }
            .productos-grid { grid-template-columns: 1fr; }
            .section-title { font-size: 1.5rem; }
        }
    </style>

    @if($productos)
        <!-- Hero -->
        <div class="hero-products">
            <h1>{{ $productos->page_title ?? 'Nuestros <span>Productos y Servicios</span>' }}</h1>
            <p>{!! $productos->page_description ?? 'Ofrecemos concreto premezclado y servicios complementarios para garantizar la excelencia en cada obra.' !!}</p>
        </div>

        <!-- ===================== TIPOS DE CONCRETO ===================== -->
        @if(!empty($tiposConcreto) && count($tiposConcreto) > 0)
            <h2 class="section-title">Tipos de <span>Concreto</span></h2>
            <div class="productos-grid">
                @foreach($tiposConcreto as $producto)
                    <div class="producto-card">
                        @if(!empty($producto['image']))
                            <img src="{{ Storage::url($producto['image']) }}" alt="{{ $producto['name'] }}">
                        @else
                            <img src="{{ asset('img/placeholder-concreto.jpg') }}" alt="{{ $producto['name'] }}">
                        @endif
                        <div class="producto-info">
                            <h3>{{ $producto['name'] }}</h3>
                            <div class="description">{!! $producto['description'] !!}</div>
                            @if(!empty($producto['price']))
                                <div class="text-[#f5811e] font-bold mt-2">S/ {{ number_format($producto['price'], 2) }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- ===================== SERVICIOS COMPLEMENTARIOS ===================== -->
        @if(!empty($servicios) && count($servicios) > 0)
            <h2 class="section-title">Servicios <span>Complementarios</span></h2>
            <div class="productos-grid">
                @foreach($servicios as $servicio)
                    <div class="producto-card">
                        @if(!empty($servicio['image']))
                            <img src="{{ Storage::url($servicio['image']) }}" alt="{{ $servicio['name'] }}">
                        @else
                            <img src="{{ asset('img/placeholder-servicio.jpg') }}" alt="{{ $servicio['name'] }}">
                        @endif
                        <div class="producto-info">
                            <h3>{{ $servicio['name'] }}</h3>
                            <div class="description">{!! $servicio['description'] !!}</div>
                            @if(!empty($servicio['price']))
                                <div class="text-[#f5811e] font-bold mt-2">S/ {{ number_format($servicio['price'], 2) }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 3rem;">
            <i class="bi bi-gear" style="font-size: 4rem; color: #9ca3af;"></i>
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #4b5563;">Configuración pendiente</h2>
            <p style="color: #6b7280;">Por favor, ingresa al panel de administración para configurar los productos.</p>
            <a href="{{ url('/admin/productos/create') }}" class="btn-cotizar" style="background-color: var(--color-primary);">Ir al Panel</a>
        </div>
    @endif
@endsection