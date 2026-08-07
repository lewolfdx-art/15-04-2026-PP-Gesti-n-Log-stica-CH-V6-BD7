@extends('layouts.app')

@section('title', $producto->name . ' - Concretera Mantaro')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <nav class="text-sm mb-6">
            <a href="{{ route('productos.index') }}" class="text-gray-500 hover:text-[#f5811e]">← Volver a productos</a>
        </nav>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Imagen -->
            <div class="bg-white rounded-xl overflow-hidden shadow-lg">
                @if($producto->image)
                    <img src="{{ Storage::url($producto->image) }}" alt="{{ $producto->name }}" class="w-full h-96 object-cover">
                @else
                    <img src="{{ asset('img/placeholder-producto.jpg') }}" alt="{{ $producto->name }}" class="w-full h-96 object-cover">
                @endif
            </div>

            <!-- Información -->
            <div>
                <h1 class="text-3xl font-bold text-[#16222e] mb-2">{{ $producto->name }}</h1>
                
                <div class="text-3xl font-bold text-[#f5811e] mb-4">
                    S/ {{ number_format($producto->price, 2) }}
                </div>

                <div class="prose max-w-none mb-6">
                    <h3 class="text-lg font-semibold text-[#16222e] mb-2">Descripción</h3>
                    <div class="text-gray-600">{!! $producto->description !!}</div>
                </div>

                @if($producto->technical_specs && count($producto->technical_specs) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-[#16222e] mb-3">Especificaciones Técnicas</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @foreach($producto->technical_specs as $spec)
                                <div class="flex justify-between py-2 border-b border-gray-200 last:border-0">
                                    <span class="font-medium text-gray-700">{{ $spec['spec_name'] }}</span>
                                    <span class="text-gray-600">{{ $spec['spec_value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="#" class="btn-cotizar inline-block text-center w-full">
                    Solicitar Cotización <i class="bi bi-whatsapp ms-2"></i>
                </a>
            </div>
        </div>
    </div>
@endsection